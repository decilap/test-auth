<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'notification_settings')]
class NotificationSetting
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'notificationSetting', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;
    
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $pushEnabled = true;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $emailEnabled = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $smsEnabled = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $perType = null;
    
    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $updatedAt;
    
    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
    }
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getPushEnabled(): bool
    {
        return $this->pushEnabled;
    }
    
    public function setPushEnabled(bool $pushEnabled): self
    {
        $this->pushEnabled = $pushEnabled;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getEmailEnabled(): bool
    {
        return $this->emailEnabled;
    }
    
    public function setEmailEnabled(bool $emailEnabled): self
    {
        $this->emailEnabled = $emailEnabled;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getSmsEnabled(): bool
    {
        return $this->smsEnabled;
    }
    
    public function setSmsEnabled(bool $smsEnabled): self
    {
        $this->smsEnabled = $smsEnabled;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getPerType(): ?array
    {
        return $this->perType;
    }
    
    public function setPerType(?array $perType): self
    {
        $this->perType = $perType;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
