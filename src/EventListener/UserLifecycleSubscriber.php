<?php

namespace App\EventListener;

use App\Entity\User;
use App\Security\RefreshTokenRevoker;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class UserLifecycleSubscriber
{
    public function __construct(
        private readonly RefreshTokenRevoker $refreshTokenRevoker
    ) {}

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $passwordChanged = $event->hasChangedField('password');
        $emailChanged = $event->hasChangedField('email');
        $accountDeactivated =
            $event->hasChangedField('isActive') &&
            $event->getNewValue('isActive') === false;

        if (!$passwordChanged && !$emailChanged && !$accountDeactivated) {
            return;
        }

        $this->refreshTokenRevoker->revokeForUser($user);
    }
}
