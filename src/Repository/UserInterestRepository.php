<?php

namespace App\Repository;

use App\Entity\UserInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterest::class);
    }

    /**
     * Récupère les intérêts d'un utilisateur avec leur poids.
     */
    public function findByUserId(string $userId): array
    {
        return $this->createQueryBuilder('ui')
            ->where('ui.user_id = :id')
            ->setParameter('id', $userId)
            ->orderBy('ui.weight', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
