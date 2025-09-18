<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * Trouve la conversation d'un match.
     */
    public function findByMatchId(string $matchId): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->where('c.match = :id')
            ->setParameter('id', $matchId)
            ->getQuery() // <-- C'est l'étape manquante
            ->getOneOrNullResult();
    }
}
