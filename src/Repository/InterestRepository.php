<?php

namespace App\Repository;

use App\Entity\Interest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interest::class);
    }

    /**
     * Recherche des intérêts par nom ou catégorie.
     */
    public function findByNameOrCategory(string $term): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.name ILIKE :term OR i.category ILIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }
}
