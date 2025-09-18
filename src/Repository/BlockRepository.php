<?php

namespace App\Repository;

use App\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Block::class);
    }

    /**
     * Vérifie si l'utilisateur a bloqué un autre utilisateur.
     */
    public function isBlocked(string $blockerId, string $blockedId): bool
    {
        return (bool)$this->createQueryBuilder('b')
            ->where('b.blocker_user_id = :blocker AND b.blocked_user_id = :blocked')
            ->setParameter('blocker', $blockerId)
            ->setParameter('blocked', $blockedId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
