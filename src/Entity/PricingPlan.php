<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'pricing_plans')]
class PricingPlan
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;
    
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $code;
    
    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $features = null;

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
    
    public function getCode(): string
    {
        return $this->code;
    }
    
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
    
    public function getFeatures(): ?array
    {
        return $this->features;
    }
    
    public function setFeatures(?array $features): self
    {
        $this->features = $features;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
