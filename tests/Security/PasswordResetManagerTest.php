<?php

namespace App\Tests\Security;

use App\Entity\EmailVerificationToken;
use App\Entity\RefreshToken;
use App\Entity\ResetPasswordToken;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Security\PasswordResetManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetManagerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PasswordResetManager $manager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->manager = $container->get(PasswordResetManager::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = array_filter(
            $this->entityManager->getMetadataFactory()->getAllMetadata(),
            static fn ($meta) => \in_array($meta->getName(), [User::class, UserProfile::class, EmailVerificationToken::class, ResetPasswordToken::class, RefreshToken::class], true)
        );
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testResetTokenIsSingleUse(): void
    {
        $user = new User();
        $user->setEmail('reset-test@example.com');
        $user->markEmailVerified();
        $hashed = $this->passwordHasher->hashPassword($user, 'StrongPassw0rd!');
        $user->setPassword($hashed);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = $this->manager->request($user->getEmail(), '127.0.0.1', true);
        self::assertNotNull($token);

        $newPassword = 'NewPassw0rd!++';
        self::assertTrue($this->manager->reset($token, $newPassword, '127.0.0.1'));

        // Password updated
        self::assertTrue($this->passwordHasher->isPasswordValid($user, $newPassword));

        // Reuse of the token must fail
        self::assertFalse($this->manager->reset($token, 'AnotherPassword!23', '127.0.0.1'));
    }
}
