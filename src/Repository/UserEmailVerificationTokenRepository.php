<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserEmailVerificationToken;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserEmailVerificationToken>
 */
class UserEmailVerificationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEmailVerificationToken::class);
    }

    public function findValidToken(string $plainToken): ?UserEmailVerificationToken
    {
        $hash = UserEmailVerificationToken::hashToken($plainToken);

        return $this->createQueryBuilder('token')
            ->andWhere('token.tokenHash = :hash')
            ->andWhere('token.expiresAt > :now')
            ->andWhere('token.consumedAt IS NULL')
            ->setParameter('hash', $hash)
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function purgeExpired(): int
    {
        return $this->createQueryBuilder('token')
            ->delete()
            ->where('token.expiresAt <= :now')
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->execute();
    }

    public function revokeAllForUser(User $user): int
    {
        return $this->createQueryBuilder('token')
            ->delete()
            ->where('token.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
