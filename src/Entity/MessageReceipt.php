<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'message_receipts')]
class MessageReceipt
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Message::class)]
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Message $message = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'recipient_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $recipient = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $deliveredAt = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $seenAt = null;

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;
        return $this;
    }
    
    public function getRecipient(): ?User
    {
        return $this->recipient;
    }
    
    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }
    
    public function getDeliveredAt(): ?DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?DateTimeImmutable $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;
        return $this;
    }

    public function getSeenAt(): ?DateTimeImmutable
    {
        return $this->seenAt;
    }

    public function setSeenAt(?DateTimeImmutable $seenAt): self
    {
        $this->seenAt = $seenAt;
        return $this;
    }
}
