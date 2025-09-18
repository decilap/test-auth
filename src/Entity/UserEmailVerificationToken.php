<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserEmailVerificationTokenRepository;

#[ORM\Entity(repositoryClass: UserEmailVerificationTokenRepository::class)]
#[ORM\Table(name: 'user_email_verification_tokens')]
#[ORM\Index(name: 'idx_verification_user', columns: ['user_id'])]
class UserEmailVerificationToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'emailVerificationTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'string', length: 128, unique: true)]
    private string $tokenHash;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $consumedAt = null;

    public function __construct(User $user, string $tokenHash, DateTimeImmutable $expiresAt)
    {
        $this->user = $user;
        $this->tokenHash = $tokenHash;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function matches(string $token): bool
    {
        return hash_equals($this->tokenHash, self::hashToken($token));
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new DateTimeImmutable();
    }

    public function getConsumedAt(): ?DateTimeImmutable
    {
        return $this->consumedAt;
    }

    public function isConsumed(): bool
    {
        return null !== $this->consumedAt;
    }

    public function markConsumed(): void
    {
        $this->consumedAt = new DateTimeImmutable();
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
