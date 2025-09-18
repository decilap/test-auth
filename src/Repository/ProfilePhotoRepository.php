<?php

namespace App\Repository;

use App\Entity\ProfilePhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfilePhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilePhoto::class);
    }

    /**
     * Récupère tous les photos d'un utilisateur.
     */
    public function findByUserId(string $userId): array
    {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * Trouve la photo principale d’un utilisateur.
     */
    public function findPrimaryByUser(string $userId): ?ProfilePhoto
    {
        return $this->findOneBy([
            'user_id' => $userId,
            'is_primary' => true
        ]);
    }
}
