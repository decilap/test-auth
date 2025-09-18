<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\PaymentStatusEnum;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'transactions')]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;
    
    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    #[ORM\JoinColumn(name: 'subscription_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Subscription $subscription = null;
    
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stripePaymentIntentId = null;
    
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stripeChargeId = null;
    
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stripeInvoiceId = null;
    
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;
    
    #[ORM\Column(type: 'integer')]
    private int $amountCents;
    
    #[ORM\Column(type: 'string', enumType: PaymentStatusEnum::class)]
    private PaymentStatusEnum $status;
    
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $rawPayload = null;

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
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }
    
    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;
        return $this;
    }
    
    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }
    
    public function setStripePaymentIntentId(?string $stripePaymentIntentId): self
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;
        return $this;
    }
    
    public function getStripeChargeId(): ?string
    {
        return $this->stripeChargeId;
    }
    
    public function setStripeChargeId(?string $stripeChargeId): self
    {
        $this->stripeChargeId = $stripeChargeId;
        return $this;
    }
    
    public function getStripeInvoiceId(): ?string
    {
        return $this->stripeInvoiceId;
    }
    
    public function setStripeInvoiceId(?string $stripeInvoiceId): self
    {
        $this->stripeInvoiceId = $stripeInvoiceId;
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
    
    public function getStatus(): PaymentStatusEnum
    {
        return $this->status;
    }
    
    public function setStatus(PaymentStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function getRawPayload(): ?array
    {
        return $this->rawPayload;
    }
    
    public function setRawPayload(?array $rawPayload): self
    {
        $this->rawPayload = $rawPayload;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
