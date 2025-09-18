<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'reports')]
class Report
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'reporter_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $reporter = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'reported_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $reportedUser = null;

    #[ORM\Column(type: 'text')]
    private string $reasonCode;
    
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details = null;
    
    #[ORM\Column(type: 'text', options: ['default' => 'open'])]
    private string $status = 'open';

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'handled_by', referencedColumnName: 'id', nullable: true)]
    private ?User $handledBy = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): self
    {
        $this->reporter = $reporter;
        return $this;
    }

    public function getReportedUser(): ?User
    {
        return $this->reportedUser;
    }

    public function setReportedUser(?User $reportedUser): self
    {
        $this->reportedUser = $reportedUser;
        return $this;
    }

    public function getReasonCode(): string
    {
        return $this->reasonCode;
    }

    public function setReasonCode(string $reasonCode): self
    {
        $this->reasonCode = $reasonCode;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): self
    {
        $this->closedAt = $closedAt;
        return $this;
    }

    public function getHandledBy(): ?User
    {
        return $this->handledBy;
    }

    public function setHandledBy(?User $handledBy): self
    {
        $this->handledBy = $handledBy;
        return $this;
    }
}
