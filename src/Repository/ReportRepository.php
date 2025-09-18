<?php

namespace App\Repository;

use App\Entity\Report;
use App\Entity\Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    /**
     * Récupère les rapports ouverts d'un utilisateur.
     */
    public function findOpenReportsForUser(string $userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.reported_user_id = :id AND r.status = :status')
            ->setParameter('id', $userId)
            ->setParameter('status', 'open')
            ->getQuery()
            ->getResult();
    }
}
