<?php

namespace App\Repository;

use App\Entity\UserLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Jsor\Doctrine\PostGIS\Types\GeographyType;

class UserLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLocation::class);
    }

    /**
     * Récupère la dernière localisation d'un utilisateur.
     */
    public function findLatestByUser(string $userId): ?UserLocation
    {
        return $this->createQueryBuilder('ul')
            ->where('ul.user_id = :id')
            ->setParameter('id', $userId)
            ->orderBy('ul.recorded_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Calcule la distance (en mètres) entre deux enregistrements UserLocation.
     * * @param UserLocation $locationA
     * @param UserLocation $locationB
     * @return float|null La distance en mètres, ou null si un point est manquant.
     */
    public function getDistanceBetween(UserLocation $locationA, UserLocation $locationB): ?float
    {
        if (!$locationA->getPoint() || !$locationB->getPoint()) {
            return null;
        }

        $dql = 'SELECT ST_Distance(a.point, b.point) 
                FROM App\Entity\UserLocation a, App\Entity\UserLocation b 
                WHERE a.id = :idA AND b.id = :idB';

        $query = $this->getEntityManager()->createQuery($dql)
            ->setParameter('idA', $locationA->getId())
            ->setParameter('idB', $locationB->getId());

        return (float) $query->getSingleScalarResult();
    }

    public function findNearbyLocations(UserLocation $location, int $radiusInMeters): array
    {
        $referencePoint = $location->getPoint();

        if (!$referencePoint) {
            return [];
        }

        return $this->createQueryBuilder('ul')
            ->andWhere('ul.id != :locationId')
            ->andWhere('ST_Distance(ul.point, :point) <= :radius')
            ->orderBy('ST_Distance(ul.point, :point)', 'ASC')
            ->setParameter('locationId', $location->getId())
            ->setParameter('point', $referencePoint, GeographyType::GEOGRAPHY)
            ->setParameter('radius', $radiusInMeters)
            ->getQuery()
            ->getResult();
    }
}
