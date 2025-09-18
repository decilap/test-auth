<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\NotifTypeEnum;
use App\Enum\NotifChannelEnum;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;
    
    #[ORM\Column(type: 'string', enumType: NotifTypeEnum::class)]
    private NotifTypeEnum $type;
    
    #[ORM\Column(type: 'string', enumType: NotifChannelEnum::class, options: ['default' => 'in_app'])]
    private NotifChannelEnum $channel = NotifChannelEnum::InApp;
    
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $title = null;
    
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $body = null;
    
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $data = null;
    
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isRead = false;
    
    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;
    
    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $readAt = null;

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
    
    public function getType(): NotifTypeEnum
    {
        return $this->type;
    }
    
    public function setType(NotifTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }
    
    public function getChannel(): NotifChannelEnum
    {
        return $this->channel;
    }
    
    public function setChannel(NotifChannelEnum $channel): self
    {
        $this->channel = $channel;
        return $this;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }
    
    public function getBody(): ?string
    {
        return $this->body;
    }
    
    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }
    
    public function getData(): ?array
    {
        return $this->data;
    }
    
    public function setData(?array $data): self
    {
        $this->data = $data;
        return $this;
    }
    
    public function getIsRead(): bool
    {
        return $this->isRead;
    }
    
    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        return $this;
    }
    
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }
    
    public function setReadAt(?DateTimeImmutable $readAt): self
    {
        $this->readAt = $readAt;
        return $this;
    }
}
