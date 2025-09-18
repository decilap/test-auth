<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserEmailVerificationToken;
use App\Repository\UserEmailVerificationTokenRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;

class EmailVerificationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserEmailVerificationTokenRepository $tokenRepository,
        #[Autowire(service: 'monolog.logger.audit')]
        private readonly LoggerInterface $auditLogger,
        private readonly string $tokenTtl = 'P1D'
    ) {
    }

    public function issueToken(User $user): string
    {
        $this->tokenRepository->revokeAllForUser($user);

        $plainToken = Uuid::v4()->toRfc4122();
        $expiresAt = (new DateTimeImmutable())->add(new DateInterval($this->tokenTtl));

        $token = new UserEmailVerificationToken($user, UserEmailVerificationToken::hashToken($plainToken), $expiresAt);
        $user->addEmailVerificationToken($token);

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $this->auditLogger->info('Email verification token issued.', [
            'userId' => $user->getId(),
            'expiresAt' => $expiresAt->format(DATE_ATOM),
        ]);

        return $plainToken;
    }

    public function confirmToken(string $plainToken): ?User
    {
        $token = $this->tokenRepository->findValidToken($plainToken);
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        $token->markConsumed();
        $user->markEmailVerified();

        $this->entityManager->flush();

        $this->auditLogger->notice('User email verified.', [
            'userId' => $user->getId(),
        ]);

        return $user;
    }

    public function purgeExpiredTokens(): int
    {
        $count = $this->tokenRepository->purgeExpired();

        if ($count > 0) {
            $this->auditLogger->info('Expired email verification tokens purged.', [
                'count' => $count,
            ]);
        }

        return $count;
    }
}
