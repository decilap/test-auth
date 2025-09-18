<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): User {
        if (!$data instanceof UserInput) {
            throw new \InvalidArgumentException('Expected UserInput DTO');
        }

        $user = new User();
        $user->setEmail($data->email);

        $hashed = $this->passwordHasher->hashPassword($user, $data->plainPassword);
        $user->setPassword($hashed);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
