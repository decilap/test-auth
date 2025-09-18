<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Substitution::class);
    }

    /**
     * Trouve l'abonnement actif d'un utilisateur.
     */
    public function findActiveSubscription(string $userId): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->where('s.user_id = :user AND s.status = :status')
            ->setParameter('user', $userId)
            ->setParameter('status', 'active')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
