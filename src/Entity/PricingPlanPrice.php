<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'pricing_plan_prices')]
class PricingPlanPrice
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;
    
    #[ORM\ManyToOne(targetEntity: PricingPlan::class)]
    #[ORM\JoinColumn(name: 'plan_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?PricingPlan $plan = null;
    
    #[ORM\Column(type: 'text')]
    private string $billingPeriod;
    
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;
    
    #[ORM\Column(type: 'integer')]
    private int $amountCents;
    
    #[ORM\Column(type: 'text', unique: true, nullable: true)]
    private ?string $stripePriceId = null;
    
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
    
    public function getId(): ?string
    {
        return $this->id;
    }
    
    public function getPlan(): ?PricingPlan
    {
        return $this->plan;
    }
    
    public function setPlan(?PricingPlan $plan): self
    {
        $this->plan = $plan;
        return $this;
    }
    
    public function getBillingPeriod(): string
    {
        return $this->billingPeriod;
    }
    
    public function setBillingPeriod(string $billingPeriod): self
    {
        $this->billingPeriod = $billingPeriod;
        return $this;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
    
    public function getAmountCents(): int
    {
        return $this->amountCents;
    }
    
    public function setAmountCents(int $amountCents): self
    {
        $this->amountCents = $amountCents;
        return $this;
    }
    
    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }
    
    public function setStripePriceId(?string $stripePriceId): self
    {
        $this->stripePriceId = $stripePriceId;
        return $this;
    }
    
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
    
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
