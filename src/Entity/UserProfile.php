<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\GenderEnum;
use App\Enum\OrientationEnum;
use App\Enum\PrivacyEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['userProfile:read']],
    denormalizationContext: ['groups' => ['userProfile:write']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'user_profiles')]
class UserProfile
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')] // <--- Add this
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')] // <--- Add this
    private ?string $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userProfile', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['user:write'])]
    private ?string $displayName = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['user:write'])]
    private ?DateTimeImmutable $birthdate = null;

    #[ORM\Column(type: 'string', enumType: GenderEnum::class, nullable: true)]
    #[Groups(['user:write'])]
    private ?GenderEnum $gender = null;

    #[ORM\Column(type: 'string', enumType: OrientationEnum::class, nullable: true)]
    #[Groups(['user:write'])]
    private ?OrientationEnum $orientation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user:write'])]
    private ?string $bio = null;

    #[ORM\Column(type: 'string', length: 120, nullable: true)]
    #[Groups(['user:write'])]
    private ?string $jobTitle = null;

    #[ORM\Column(type: 'string', length: 160, nullable: true)]
    #[Groups(['user:write'])]
    private ?string $company = null;

    #[ORM\Column(type: 'string', length: 160, nullable: true)]
    #[Groups(['user:write'])]
    private ?string $education = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['user:write'])]
    private ?int $heightCm = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['user:write'])]
    private ?array $lifestyleJson = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['user:write'])]
    private ?array $preferencesJson = null;

    #[ORM\Column(type: 'string', enumType: PrivacyEnum::class, options: ['default' => 'everyone'])]
    #[Groups(['user:write'])]
    private PrivacyEnum $visibility = PrivacyEnum::Everyone;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['user:write'])]
    private bool $photoVerification = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['user:write'])]
    private ?int $locationAccuracyM = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:write'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:write'])]
    private DateTimeImmutable $updatedAt;
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        if ($user !== null && $user->getUserProfile() !== $this) {
            $user->setUserProfile($this);
        }
        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getBirthdate(): ?DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(?DateTimeImmutable $birthdate): self
    {
        $this->birthdate = $birthdate;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function setGender(?GenderEnum $gender): self
    {
        $this->gender = $gender;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getOrientation(): ?OrientationEnum
    {
        return $this->orientation;
    }

    public function setOrientation(?OrientationEnum $orientation): self
    {
        $this->orientation = $orientation;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setEducation(?string $education): self
    {
        $this->education = $education;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getHeightCm(): ?int
    {
        return $this->heightCm;
    }

    public function setHeightCm(?int $heightCm): self
    {
        $this->heightCm = $heightCm;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getLifestyleJson(): ?array
    {
        return $this->lifestyleJson;
    }

    public function setLifestyleJson(?array $lifestyleJson): self
    {
        $this->lifestyleJson = $lifestyleJson;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getPreferencesJson(): ?array
    {
        return $this->preferencesJson;
    }

    public function setPreferencesJson(?array $preferencesJson): self
    {
        $this->preferencesJson = $preferencesJson;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getVisibility(): PrivacyEnum
    {
        return $this->visibility;
    }

    public function setVisibility(PrivacyEnum $visibility): self
    {
        $this->visibility = $visibility;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getPhotoVerification(): bool
    {
        return $this->photoVerification;
    }

    public function setPhotoVerification(bool $photoVerification): self
    {
        $this->photoVerification = $photoVerification;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getLocationAccuracyM(): ?int
    {
        return $this->locationAccuracyM;
    }

    public function setLocationAccuracyM(?int $locationAccuracyM): self
    {
        $this->locationAccuracyM = $locationAccuracyM;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
