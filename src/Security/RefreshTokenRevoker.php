<?php

namespace App\Security;

use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RefreshTokenRevoker
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire(service: 'monolog.logger.security')]
        private readonly LoggerInterface $logger
    ) {
    }

    public function revokeForUser(User $user): int
    {
        $username = $user->getEmail();

        $qb = $this->entityManager->createQueryBuilder();
        $deleted = $qb
            ->delete(RefreshToken::class, 'token')
            ->where('token.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->execute();

        if ($deleted > 0) {
            $this->logger->notice('Refresh tokens revoked after account change.', [
                'userId' => $user->getId(),
                'username' => $username,
                'revoked_count' => $deleted,
            ]);
        }

        return $deleted;
    }
}
