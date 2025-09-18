<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'conversations')]
class Conversation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\OneToOne(targetEntity: UserMatch::class)]
    #[ORM\JoinColumn(name: 'match_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?UserMatch $match = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;
    
    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $lastMsgAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
    
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMatch(): ?UserMatch
    {
        return $this->match;
    }

    public function setMatch(?UserMatch $match): self
    {
        $this->match = $match;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastMsgAt(): ?DateTimeImmutable
    {
        return $this->lastMsgAt;
    }

    public function setLastMsgAt(?DateTimeImmutable $lastMsgAt): self
    {
        $this->lastMsgAt = $lastMsgAt;
        return $this;
    }
}
