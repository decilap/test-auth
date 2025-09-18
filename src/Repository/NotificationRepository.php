<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Récupère les notifications non lues d'un utilisateur.
     */
    public function findUnreadByUser(string $userId): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.user_id = :id AND n.is_read = FALSE')
            ->setParameter('id', $userId)
            ->orderBy('n.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead(string $notifId, string $userId): void
    {
        $em = $this->getEntityManager();
        $q = $em->createQuery('UPDATE App\Entity\Notifications n SET n.is_read = TRUE, n.read_at = CURRENT_TIMESTAMP WHERE n.id = :id AND n.user_id = :user');
        $q->setParameter('id', $notifId)->setParameter('user', $userId);
        $q->execute();
    }
}
