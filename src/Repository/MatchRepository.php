<?php

namespace App\Repository;

use App\Entity\UserMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMatch::class);
    }

    /**
     * Trouve un match par utilisateur (a ou b).
     */
    public function findByUser(string $userId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.user_a_id = :id OR m.user_b_id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les matchs actifs.
     */
    public function findActiveMatches(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = :status')
            ->setParameter('status', 'active')
            ->getQuery()
            ->getResult();
    }
}
