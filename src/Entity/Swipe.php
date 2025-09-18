<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\LikeTypeEnum;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'swipes')]
class Swipe
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'actor_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $actorUser = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'target_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $targetUser = null;

    #[ORM\Column(type: 'string', enumType: LikeTypeEnum::class)]
    private LikeTypeEnum $action;

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

    public function getActorUser(): ?User
    {
        return $this->actorUser;
    }

    public function setActorUser(?User $actorUser): self
    {
        $this->actorUser = $actorUser;
        return $this;
    }

    public function getTargetUser(): ?User
    {
        return $this->targetUser;
    }

    public function setTargetUser(?User $targetUser): self
    {
        $this->targetUser = $targetUser;
        return $this;
    }

    public function getAction(): LikeTypeEnum
    {
        return $this->action;
    }

    public function setAction(LikeTypeEnum $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
