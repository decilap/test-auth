<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    formats: ['jsonld'],
    denormalizationContext: ['groups' => ['user:write']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'user_interests', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'user_interest_unique', columns: ['user_id', 'interest_id'])
])]
class UserInterest
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userInterests')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Interest::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:write'])] // Ajout de ce groupe
    private ?Interest $interest = null;

    #[ORM\Column(type: 'smallint', options: ['default' => 1])]
    private int $weight = 1;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
    
    // Ajout d'un getter pour l'ID
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

    public function getInterest(): ?Interest
    {
        return $this->interest;
    }

    public function setInterest(?Interest $interest): self
    {
        $this->interest = $interest;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}