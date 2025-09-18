<?php

namespace App\Repository;

use App\Entity\MediaAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MediaAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaAsset::class);
    }

    /**
     * Trouve tous les médias d'un utilisateur.
     */
    public function findByUserId(string $userId): array
    {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * Récupère un média par clé stockage (ex: S3).
     */
    public function findOneByStorageKey(string $key): ?MediaAsset
    {
        return $this->findOneBy(['storage_key' => $key]);
    }
}
