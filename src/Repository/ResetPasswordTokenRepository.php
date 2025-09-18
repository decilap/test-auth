<?php

namespace App\Repository;

use App\Entity\ResetPasswordToken;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResetPasswordToken>
 */
class ResetPasswordTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordToken::class);
    }

    public function findValidByHash(string $hash): ?ResetPasswordToken
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.tokenHash = :hash')
            ->andWhere('token.expiresAt > :now')
            ->andWhere('token.consumedAt IS NULL')
            ->setParameter('hash', $hash)
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
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

    public function purgeExpired(): int
    {
        return $this->createQueryBuilder('token')
            ->delete()
            ->where('token.expiresAt <= :now')
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
