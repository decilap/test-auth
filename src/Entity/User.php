<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\UserInput;
use App\Entity\EmailVerificationToken;
use App\Enum\VerificationEnum;
use App\Repository\UserRepository;
use App\State\UserPasswordProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Post(
            input: UserInput::class,
            processor: UserPasswordProcessor::class
        ),
        new Get(),            // GET /api/users/{id}
        new GetCollection()   // GET /api/users
    ],
    denormalizationContext: ['groups' => ['user:write']],
    normalizationContext: ['groups' => ['user:read']]
)]
#[Post(
    input: UserInput::class,
    processor: UserPasswordProcessor::class
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private string $email;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private string $emailNormalized;

    #[ORM\Column(type: 'text')]
    #[Groups(['user:write'])]
    private string $password;

    private ?string $plainPassword = null;

    #[ORM\Column(type: 'text', options: ['default' => 'argon2id'])]
    private string $passwordAlgo = 'argon2id';

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phoneE164 = null;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $username = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_roles')]
    private Collection $roles;

    #[ORM\Column(type: 'string', enumType: VerificationEnum::class, options: ['default' => 'unverified'])]
    #[Groups(['user:read'])]
    private VerificationEnum $verificationLevel = VerificationEnum::Unverified;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    #[Groups(['user:read'])]
    private ?DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['user:read'])]
    private bool $isActive = false;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:read'])]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    #[Groups(['user:read'])]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserProfile::class, cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private ?UserProfile $userProfile = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserLocation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['user:write'])]
    private Collection $userLocations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserInterest::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['user:write'])] // C'est cette ligne qui résout le problème
    private Collection $userInterests;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EmailVerificationToken::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $emailVerificationTokens;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    #[Groups(['user:read'])]
    private ?DateTimeImmutable $emailVerifiedAt = null;

    public function __construct()
    {
        $this->userInterests = new ArrayCollection();
        $this->userLocations = new ArrayCollection();
        $this->emailVerificationTokens = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        $this->emailNormalized = strtolower($email);
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getEmailNormalized(): string
    {
        return $this->emailNormalized;
    }

    public function setEmailNormalized(string $emailNormalized): self
    {
        $this->emailNormalized = strtolower($emailNormalized);
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getPasswordAlgo(): string
    {
        return $this->passwordAlgo;
    }

    public function setPasswordAlgo(string $passwordAlgo): self
    {
        $this->passwordAlgo = $passwordAlgo;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getPhoneE164(): ?string
    {
        return $this->phoneE164;
    }

    public function setPhoneE164(?string $phoneE164): self
    {
        $this->phoneE164 = $phoneE164;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * Example: ['ROLE_USER', 'ROLE_ADMIN']
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles->map(function (Role $role) {
            return $role->getName();
        })->toArray();
    }

    /**
     * @param Collection<int, Role> $roles
     */
    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Ajoute un rôle à l'utilisateur
     *
     * @param Role $role
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * Supprime un rôle de l'utilisateur
     *
     * @param Role $role
     */
    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * Vérifie si l'utilisateur a un rôle donné (ex: ROLE_ADMIN)
     */
    public function hasRole(string $roleName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getName() === $roleName) {
                return true;
            }
        }

        return false;
    }

    public function getRoleNames(): array
    {
        return $this->roles->map(function (Role $role) {
            return $role->getName();
        })->toArray();
    }

    public function getVerificationLevel(): VerificationEnum
    {
        return $this->verificationLevel;
    }

    public function setVerificationLevel(VerificationEnum $verificationLevel): self
    {
        $this->verificationLevel = $verificationLevel;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getLastLoginAt(): ?DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeImmutable $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function isEmailVerified(): bool
    {
        return null !== $this->emailVerifiedAt;
    }

    public function markEmailVerified(): self
    {
        if (!$this->isEmailVerified()) {
            $this->emailVerifiedAt = new DateTimeImmutable();
            $this->verificationLevel = VerificationEnum::EmailVerified;
            $this->isActive = true;
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function clearEmailVerifiedAt(): self
    {
        $this->emailVerifiedAt = null;
        $this->verificationLevel = VerificationEnum::Unverified;
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function getEmailVerificationTokens(): Collection
    {
        return $this->emailVerificationTokens;
    }

    public function addEmailVerificationToken(EmailVerificationToken $token): self
    {
        if (!$this->emailVerificationTokens->contains($token)) {
            $this->emailVerificationTokens->add($token);
        }

        return $this;
    }

    public function removeEmailVerificationToken(EmailVerificationToken $token): self
    {
        $this->emailVerificationTokens->removeElement($token);

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;
        if ($userProfile !== null && $userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }
        return $this;
    }

    /**
     * @return Collection|UserLocation[]
     */
    public function getUserLocations(): Collection
    {
        return $this->userLocations;
    }

    public function addUserLocation(UserLocation $userLocation): self
    {
        if (!$this->userLocations->contains($userLocation)) {
            $this->userLocations[] = $userLocation;
            $userLocation->setUser($this);
        }
        return $this;
    }

    public function removeUserLocation(UserLocation $userLocation): self
    {
        if ($this->userLocations->removeElement($userLocation)) {
            if ($userLocation->getUser() === $this) {
                $userLocation->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|UserInterest[]
     */
    public function getUserInterests(): Collection
    {
        return $this->userInterests;
    }

    public function addUserInterest(UserInterest $userInterest): self
    {
        if (!$this->userInterests->contains($userInterest)) {
            $this->userInterests[] = $userInterest;
            $userInterest->setUser($this);
        }
        return $this;
    }

    public function removeUserInterest(UserInterest $userInterest): self
    {
        if ($this->userInterests->removeElement($userInterest)) {
            if ($userInterest->getUser() === $this) {
                $userInterest->setUser(null);
            }
        }
        return $this;
    }

    /**
     * Returns the password used for authentication.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Removes sensitive data after authentication (e.g., clears password).
     */
    public function eraseCredentials(): void
    {
        // Optional: clear any sensitive fields here if needed later
        // This is mainly for security purposes — e.g., if you stored the plain password somewhere.
        // In most cases, you don't need to do anything here unless you store plaintext.
    }

    /**
     * Returns a unique identifier for this user (e.g., email or ID).
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }


}
