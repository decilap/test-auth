<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\MatchStatusEnum;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'matches')]
class UserMatch
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_a_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $userA = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_b_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $userB = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;
    
    #[ORM\Column(type: 'string', enumType: MatchStatusEnum::class, options: ['default' => 'active'])]
    private MatchStatusEnum $status = MatchStatusEnum::Active;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserA(): ?User
    {
        return $this->userA;
    }

    public function setUserA(?User $userA): self
    {
        $this->userA = $userA;
        return $this;
    }

    public function getUserB(): ?User
    {
        return $this->userB;
    }

    public function setUserB(?User $userB): self
    {
        $this->userB = $userB;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getStatus(): MatchStatusEnum
    {
        return $this->status;
    }
    
    public function setStatus(MatchStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }
}
