<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouve un utilisateur par email (en minuscules).
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => strtolower($email)]);
    }

    /**
     * Récupère tous les utilisateurs actifs.
     */
    public function findActiveUsers(): array
    {
        return $this->findBy(['is_active' => true]);
    }

    /**
     * Trouve un utilisateur avec son profil complet (join).
     */
    public function findWithProfile(string $userId): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.profile', 'p')
            ->addSelect('p')
            ->where('u.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
