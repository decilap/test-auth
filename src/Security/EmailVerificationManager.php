<?php

namespace App\Security;

use App\Entity\EmailVerificationToken;
use App\Entity\User;
use App\Repository\EmailVerificationTokenRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Throwable;

class EmailVerificationManager
{
    private const TOKEN_TTL = 'PT20M';
    private const TOKEN_SIZE = 32;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EmailVerificationTokenRepository $tokenRepository,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $securityLogger,
        private readonly RateLimiterFactory $resendLimiter,
        private readonly RateLimiterFactory $verifyLimiter,
        private readonly string $hmacKey,
        private readonly string $frontendVerifyUrl,
        private readonly string $fromAddress,
        private readonly ?string $fromName = null
    ) {
    }

    public function dispatchVerification(User $user, string $clientIp, bool $returnPlainToken = false): ?string
    {
        [$plainToken, $expiresAt] = $this->entityManager->wrapInTransaction(function (EntityManagerInterface $em) use ($user) {
            $this->tokenRepository->revokeAllForUser($user);

            $plainToken = $this->generateToken();
            $hash = $this->hashToken($plainToken);
            $expiresAt = $this->now()->add(new DateInterval(self::TOKEN_TTL));

            $token = new EmailVerificationToken($user, $hash, $expiresAt);
            $user->clearEmailVerifiedAt();
            $user->addEmailVerificationToken($token);
            $em->persist($token);
            $em->flush();

            return [$plainToken, $expiresAt];
        });

        $this->sendEmail($user, $plainToken, $expiresAt);
        $this->log('verification_dispatched', $user, $clientIp);

        return $returnPlainToken ? $plainToken : null;
    }

    public function resendForEmail(string $email, string $clientIp): void
    {
        $emailLimiter = $this->resendLimiter->create(strtolower($email));
        $limit = $emailLimiter->consume(1);
        if (!$limit->isAccepted()) {
            $retryAfter = $limit->getRetryAfter();
            $wait = $retryAfter ? max(0, $retryAfter->getTimestamp() - time()) : null;
            throw new TooManyRequestsHttpException($wait, 'Trop de tentatives. Réessayez plus tard.');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'emailNormalized' => strtolower($email),
        ]);

        if (!$user || $user->isEmailVerified()) {
            $this->log('verification_resend_noop', null, $clientIp, ['email' => $this->maskEmail($email)]);
            return;
        }

        $this->dispatchVerification($user, $clientIp);
    }

    public function verifyToken(string $plainToken, string $clientIp): bool
    {
        $limiter = $this->verifyLimiter->create($clientIp ?: 'unknown');
        $limit = $limiter->consume(1);
        if (!$limit->isAccepted()) {
            $retryAfter = $limit->getRetryAfter();
            $wait = $retryAfter ? max(0, $retryAfter->getTimestamp() - time()) : null;
            throw new TooManyRequestsHttpException($wait, 'Trop de tentatives. Réessayez plus tard.');
        }

        $hash = $this->hashToken($plainToken);

        return $this->entityManager->wrapInTransaction(function (EntityManagerInterface $em) use ($hash, $clientIp) {
            $token = $this->tokenRepository->findValidByHash($hash);
            if (!$token) {
                $this->log('verification_failed', null, $clientIp);
                return false;
            }

            if ($token->isExpired()) {
                $this->log('verification_expired', $token->getUser(), $clientIp);
                return false;
            }

            $token->markConsumed();
            $user = $token->getUser();
            $user->markEmailVerified();

            $em->persist($token);
            $em->persist($user);
            $em->flush();

            $this->log('verification_succeeded', $user, $clientIp);

            return true;
        });
    }

    public function purgeExpiredTokens(): int
    {
        return $this->tokenRepository->purgeExpired();
    }

    private function sendEmail(User $user, string $token, DateTimeImmutable $expiresAt): void
    {
        $url = sprintf('%s?token=%s', rtrim($this->frontendVerifyUrl, '/'), $token);

        $email = (new TemplatedEmail())
            ->from(new Address($this->fromAddress, $this->fromName ?? 'LoveConnect'))
            ->to(new Address($user->getEmail()))
            ->subject('Confirme ton adresse email')
            ->htmlTemplate('emails/email_verification.html.twig')
            ->context([
                'user' => $user,
                'verification_url' => $url,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

        try {
            $this->mailer->send($email);
        } catch (Throwable $exception) {
            $this->securityLogger->error('verification_email_send_failed', [
                'user_id' => $user->getId(),
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function generateToken(): string
    {
        $randomizer = new Randomizer();
        $bytes = $randomizer->getBytes(self::TOKEN_SIZE);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    private function hashToken(string $token): string
    {
        return hash_hmac('sha256', $token, $this->hmacKey);
    }

    private function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    private function log(string $event, ?User $user, ?string $ip, array $context = []): void
    {
        $context['ip'] = $this->truncateIp($ip);
        if ($user) {
            $context['user_id'] = $user->getId();
            $context['email'] = $this->maskEmail($user->getEmail());
        }

        $this->securityLogger->notice($event, $context);
    }

    private function truncateIp(?string $ip): string
    {
        if (!$ip) {
            return 'unknown';
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0';
            return implode('.', $parts);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $segments = explode(':', $ip);
            $segments = array_pad($segments, 8, '0');
            array_splice($segments, 4);
            return implode(':', $segments) . '::';
        }

        return 'unknown';
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $maskedLocal = mb_substr($local, 0, 1) . str_repeat('*', max(0, mb_strlen($local) - 2)) . mb_substr($local, -1);
        return sprintf('%s@%s', $maskedLocal, $domain);
    }
}
