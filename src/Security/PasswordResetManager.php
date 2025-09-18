<?php

namespace App\Security;

use App\Entity\ResetPasswordToken;
use App\Entity\User;
use App\Repository\ResetPasswordTokenRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class PasswordResetManager
{
    private const TOKEN_TTL = 'PT30M';
    private const TOKEN_SIZE = 32;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly ResetPasswordTokenRepository $tokenRepository,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $securityLogger,
        private readonly RateLimiterFactory $requestEmailLimiter,
        private readonly RateLimiterFactory $requestIpLimiter,
        private readonly RateLimiterFactory $confirmLimiter,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
        private readonly string $hmacKey,
        private readonly string $frontendResetUrl,
        private readonly string $fromAddress,
        private readonly ?string $fromName = null
    ) {
    }

    public function request(string $email, string $clientIp, bool $returnPlainToken = false): ?string
    {
        $normalizedEmail = strtolower($email);
        $ipLimit = $this->requestIpLimiter->create($clientIp ?: 'unknown')->consume(1);
        if (!$ipLimit->isAccepted()) {
            $this->throwThrottle($ipLimit->getRetryAfter());
        }

        $emailLimit = $this->requestEmailLimiter->create($normalizedEmail)->consume(1);
        if (!$emailLimit->isAccepted()) {
            $this->throwThrottle($emailLimit->getRetryAfter());
        }

        $user = $this->userRepository->findOneBy(['emailNormalized' => $normalizedEmail]);
        if (!$user || !$user->isActive()) {
            $this->log('password_request_noop', null, $clientIp, ['email' => $this->maskEmail($email)]);
            return null;
        }

        [$plainToken, $expiresAt] = $this->entityManager->wrapInTransaction(function (EntityManagerInterface $em) use ($user) {
            $this->tokenRepository->revokeAllForUser($user);

            $plainToken = $this->generateToken();
            $hash = $this->hashToken($plainToken);
            $expiresAt = $this->now()->add(new DateInterval(self::TOKEN_TTL));

            $token = new ResetPasswordToken($user, $hash, $expiresAt);
            $em->persist($token);
            $em->flush();

            return [$plainToken, $expiresAt];
        });

        $this->sendEmail($user, $plainToken, $expiresAt);
        $this->log('password_request_issued', $user, $clientIp);

        return $returnPlainToken ? $plainToken : null;
    }

    public function reset(string $plainToken, string $newPassword, string $clientIp): bool
    {
        $limit = $this->confirmLimiter->create($clientIp ?: 'unknown')->consume(1);
        if (!$limit->isAccepted()) {
            $this->throwThrottle($limit->getRetryAfter());
        }

        $hash = $this->hashToken($plainToken);

        return $this->entityManager->wrapInTransaction(function (EntityManagerInterface $em) use ($hash, $newPassword, $clientIp) {
            $token = $this->tokenRepository->findValidByHash($hash);
            if (!$token || $token->isExpired()) {
                $this->log('password_reset_invalid_token', null, $clientIp);
                return false;
            }

            $user = $token->getUser();
            $violations = $this->validatePassword($newPassword);
            if (\count($violations) > 0) {
                throw new InvalidArgumentException($violations[0]->getMessage());
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $token->markConsumed();
            $this->tokenRepository->revokeAllForUser($user);

            $em->persist($user);
            $em->persist($token);
            $em->flush();

            $this->log('password_reset_success', $user, $clientIp);

            return true;
        });
    }

    public function purgeExpiredTokens(): int
    {
        return $this->tokenRepository->purgeExpired();
    }

    private function sendEmail(User $user, string $token, DateTimeImmutable $expiresAt): void
    {
        $url = sprintf('%s?token=%s', rtrim($this->frontendResetUrl, '/'), $token);

        $email = (new TemplatedEmail())
            ->from(new Address($this->fromAddress, $this->fromName ?? 'LoveConnect'))
            ->to(new Address($user->getEmail()))
            ->subject('Réinitialise ton mot de passe')
            ->htmlTemplate('emails/password_reset.html.twig')
            ->context([
                'user' => $user,
                'reset_url' => $url,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

        try {
            $this->mailer->send($email);
        } catch (Throwable $exception) {
            $this->securityLogger->error('password_reset_email_failed', [
                'user_id' => $user->getId(),
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function validatePassword(string $password): array
    {
        $constraints = [
            new Assert\NotBlank(message: 'Le mot de passe est requis.'),
            new Assert\Length(min: 12, max: 128, minMessage: 'Le mot de passe doit contenir au moins 12 caractères.'),
            new Assert\Regex(
                pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/',
                message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
            ),
            new Assert\NotCompromisedPassword,
        ];

        return iterator_to_array($this->validator->validate($password, $constraints));
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

    private function throwThrottle(?\DateTimeInterface $retryAfter): void
    {
        $seconds = $retryAfter ? max(0, $retryAfter->getTimestamp() - time()) : null;
        throw new TooManyRequestsHttpException($seconds, 'Trop de tentatives. Merci de réessayer plus tard.');
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
