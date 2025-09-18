<?php

namespace App\Repository;

use App\Entity\UserProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfile::class);
    }

    /**
     * Trouve le profil par utilisateur.
     */
    public function findOneByUserId(string $userId): ?UserProfile
    {
        return $this->findOneBy(['user_id' => $userId]);
    }

    /**
     * Récupère les profils filtrés par sexe, orientation ou lieu.
     */
    public function findActiveProfiles(
        string $gender = null,
        string $orientation = null,
        float $lat = 0.0,
        float $lng = 0.0,
        int $radiusKm = 50
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u.is_active = true')
            ->andWhere('p.visibility IN (:vis, :everyone)')
            ->setParameter('vis', ['matches', 'everyone'])
            ->setParameter('everyone', 'everyone');

        if ($gender) {
            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
        }
        if ($orientation) {
            $qb->andWhere('p.orientation = :orientation')->setParameter('orientation', $orientation);
        }

        // Ajouter la recherche géographique si nécessaire
        if ($lat && $lng) {
            $qb->addSelect(sprintf(
                'ST_Distance_Sphere(p.point, ST_GeogFromText(\'SRID=4326;POINT(%f %f)\')) AS distance_m',
                $lng, $lat
            ));
            $qb->andWhere('distance_m < :radius')->setParameter('radius', $radiusKm * 1000);
        }

        return $qb->orderBy('distance_m', 'ASC')->getQuery()->getResult();
    }
}
