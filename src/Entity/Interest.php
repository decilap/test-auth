<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;

#[ApiResource()]
#[ORM\Entity]
#[ORM\Table(name: 'interests')]
class Interest
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;
    
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;
    
    // One-to-many relation
    #[ORM\OneToMany(mappedBy: 'interest', targetEntity: UserInterest::class, cascade: ['persist', 'remove'])]
    private Collection $userInterests;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->userInterests = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): ?string
    {
        return $this->id = $id;
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
    
    public function getCategory(): ?string
    {
        return $this->category;
    }
    
    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    /**
     * @return Collection|UserInterest[]
     */
    public function getUserInterests(): Collection
    {
        return $this->userInterests;
    }
    
    public function addUserInterest(UserInterest $userInterest): self
    {
        if (!$this->userInterests->contains($userInterest)) {
            $this->userInterests[] = $userInterest;
            $userInterest->setInterest($this);
        }
        return $this;
    }
    
    public function removeUserInterest(UserInterest $userInterest): self
    {
        if ($this->userInterests->removeElement($userInterest)) {
            // set the owning side to null (unless already changed)
            if ($userInterest->getInterest() === $this) {
                $userInterest->setInterest(null);
            }
        }
        return $this;
    }
}
