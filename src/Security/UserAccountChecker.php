<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAccountChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('Votre compte est désactivé. Veuillez contacter le support.');
        }

        if (!$user->isEmailVerified()) {
            throw new CustomUserMessageAccountStatusException('Veuillez vérifier votre adresse email avant de continuer.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
