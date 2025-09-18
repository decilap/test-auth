<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use App\Security\EmailVerificationManager;

class UserPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EmailVerificationManager $verificationManager,
        private readonly RequestStack $requestStack,
        private readonly RateLimiterFactory $signupIpLimiter,
        private readonly RateLimiterFactory $signupEmailLimiter
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

        $request = $this->requestStack->getCurrentRequest();
        $clientIp = $request?->getClientIp() ?? 'unknown';
        $emailKey = strtolower($data->email);

        $ipLimit = $this->signupIpLimiter->create($clientIp)->consume(1);
        if (!$ipLimit->isAccepted()) {
            $retryAfter = $ipLimit->getRetryAfter();
            throw new TooManyRequestsHttpException($retryAfter ? max(0, $retryAfter->getTimestamp() - time()) : null, 'Trop de tentatives depuis cette adresse IP.');
        }

        $emailLimit = $this->signupEmailLimiter->create($emailKey)->consume(1);
        if (!$emailLimit->isAccepted()) {
            $retryAfter = $emailLimit->getRetryAfter();
            throw new TooManyRequestsHttpException($retryAfter ? max(0, $retryAfter->getTimestamp() - time()) : null, 'Trop de tentatives pour cette adresse email.');
        }

        $user = new User();
        $user->setEmail($data->email);
        $user->setUsername($data->username);
        $user->clearEmailVerifiedAt();

        $hashed = $this->passwordHasher->hashPassword($user, $data->plainPassword);
        $user->setPassword($hashed);

        $this->em->persist($user);
        $this->em->flush();

        $this->verificationManager->dispatchVerification($user, $clientIp);

        return $user;
    }
}
