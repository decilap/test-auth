<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = ['user', 'admin', 'moderator'];

        foreach ($roles as $roleName) {
            $role = new Role();
            $role->setName($roleName);
            $manager->persist($role);
        }

        $manager->flush();
        echo "✅ Roles chargés avec succès !\n";
    }
}
