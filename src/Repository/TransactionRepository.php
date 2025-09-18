<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Récupère les transactions d'un utilisateur.
     */
    public function findUserTransactions(string $userId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user_id = :id')
            ->setParameter('id', $userId)
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
