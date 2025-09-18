<?php

namespace App\Tests\Security;

use App\Entity\EmailVerificationToken;
use App\Entity\User;
use App\Security\EmailVerificationManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmailVerificationManagerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private EmailVerificationManager $manager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->manager = $container->get(EmailVerificationManager::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = array_filter(
            $this->entityManager->getMetadataFactory()->getAllMetadata(),
            static fn ($meta) => \in_array($meta->getName(), [User::class, EmailVerificationToken::class], true)
        );
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }

    public function testTokenCannotBeReused(): void
    {
        $user = new User();
        $user->setEmail('test-verif@example.com');
        $hashed = $this->passwordHasher->hashPassword($user, 'StrongPassw0rd!');
        $user->setPassword($hashed);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = $this->manager->dispatchVerification($user, '127.0.0.1', true);

        self::assertNotNull($token);

        self::assertTrue($this->manager->verifyToken($token, '127.0.0.1'));
        self::assertFalse($this->manager->verifyToken($token, '127.0.0.1'));
    }
}
