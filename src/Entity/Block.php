<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'blocks')]
class Block
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'blocker_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $blockerUser = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'blocked_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $blockedUser = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;
    
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
    
    public function getBlockerUser(): ?User
    {
        return $this->blockerUser;
    }

    public function setBlockerUser(?User $blockerUser): self
    {
        $this->blockerUser = $blockerUser;
        return $this;
    }
    
    public function getBlockedUser(): ?User
    {
        return $this->blockedUser;
    }
    
    public function setBlockedUser(?User $blockedUser): self
    {
        $this->blockedUser = $blockedUser;
        return $this;
    }
    
    public function getReason(): ?string
    {
        return $this->reason;
    }
    
    public function setReason(?string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
