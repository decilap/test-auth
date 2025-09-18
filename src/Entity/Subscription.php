<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\SubscriptionStatusEnum;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'subscriptions')]
class Subscription
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;
    
    #[ORM\ManyToOne(targetEntity: PricingPlanPrice::class)]
    #[ORM\JoinColumn(name: 'plan_price_id', referencedColumnName: 'id', nullable: false)]
    private ?PricingPlanPrice $planPrice = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stripeCustomerId = null;

    #[ORM\Column(type: 'text', unique: true, nullable: true)]
    private ?string $stripeSubscriptionId = null;

    #[ORM\Column(type: 'string', enumType: SubscriptionStatusEnum::class)]
    private SubscriptionStatusEnum $status;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $currentPeriodStart = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $currentPeriodEnd = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $cancelAt = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $canceledAt = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $trialStart = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $trialEnd = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->startedAt = new DateTimeImmutable();
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
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getPlanPrice(): ?PricingPlanPrice
    {
        return $this->planPrice;
    }
    
    public function setPlanPrice(?PricingPlanPrice $planPrice): self
    {
        $this->planPrice = $planPrice;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }
    
    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }
    
    public function setStripeSubscriptionId(?string $stripeSubscriptionId): self
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getStatus(): SubscriptionStatusEnum
    {
        return $this->status;
    }
    
    public function setStatus(SubscriptionStatusEnum $status): self
    {
        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getCurrentPeriodStart(): ?DateTimeImmutable
    {
        return $this->currentPeriodStart;
    }
    
    public function setCurrentPeriodStart(?DateTimeImmutable $currentPeriodStart): self
    {
        $this->currentPeriodStart = $currentPeriodStart;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getCurrentPeriodEnd(): ?DateTimeImmutable
    {
        return $this->currentPeriodEnd;
    }
    
    public function setCurrentPeriodEnd(?DateTimeImmutable $currentPeriodEnd): self
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getCancelAt(): ?DateTimeImmutable
    {
        return $this->cancelAt;
    }
    
    public function setCancelAt(?DateTimeImmutable $cancelAt): self
    {
        $this->cancelAt = $cancelAt;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getCanceledAt(): ?DateTimeImmutable
    {
        return $this->canceledAt;
    }
    
    public function setCanceledAt(?DateTimeImmutable $canceledAt): self
    {
        $this->canceledAt = $canceledAt;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getTrialStart(): ?DateTimeImmutable
    {
        return $this->trialStart;
    }
    
    public function setTrialStart(?DateTimeImmutable $trialStart): self
    {
        $this->trialStart = $trialStart;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getTrialEnd(): ?DateTimeImmutable
    {
        return $this->trialEnd;
    }
    
    public function setTrialEnd(?DateTimeImmutable $trialEnd): self
    {
        $this->trialEnd = $trialEnd;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }
    
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
