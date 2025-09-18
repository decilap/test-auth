<?php

namespace App\Repository;

use App\Entity\Swipe;
use App\Entity\Swipes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SwipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Swipe::class);
    }

    /**
     * Vérifie si un utilisateur a déjà swipé sur un autre.
     */
    public function hasSwiped(string $actorId, string $targetId): bool
    {
        return (bool)$this->createQueryBuilder('s')
            ->where('s.actor_user_id = :actor AND s.target_user_id = :target')
            ->setParameter('actor', $actorId)
            ->setParameter('target', $targetId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère les dernières actions de swiping d’un utilisateur.
     */
    public function findRecentByActor(string $userId, int $limit = 20): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.actor_user_id = :id')
            ->setParameter('id', $userId)
            ->orderBy('s.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
