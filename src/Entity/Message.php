<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\MsgTypeEnum;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'messages')]
class Message
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(name: 'conversation_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'sender_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $sender = null;

    #[ORM\Column(type: 'string', enumType: MsgTypeEnum::class, options: ['default' => 'text'])]
    private MsgTypeEnum $messageType = MsgTypeEnum::Text;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $body = null;

    #[ORM\ManyToOne(targetEntity: MediaAsset::class)]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id', nullable: true)]
    private ?MediaAsset $media = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;
    
    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $editedAt = null;
    
    #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;
    
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getMessageType(): MsgTypeEnum
    {
        return $this->messageType;
    }

    public function setMessageType(MsgTypeEnum $messageType): self
    {
        $this->messageType = $messageType;
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

    public function getMedia(): ?MediaAsset
    {
        return $this->media;
    }

    public function setMedia(?MediaAsset $media): self
    {
        $this->media = $media;
        return $this;
    }
    
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }
    
    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getEditedAt(): ?DateTimeImmutable
    {
        return $this->editedAt;
    }
    
    public function setEditedAt(?DateTimeImmutable $editedAt): self
    {
        $this->editedAt = $editedAt;
        return $this;
    }
    
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
    
    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
