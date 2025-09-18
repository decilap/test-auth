<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Jsor\Doctrine\PostGIS\Types\PostGISType; // Assurez-vous d'avoir ce use, ou définissez 'geometry'/'geography' dans doctrine.yaml

#[ORM\Entity]
#[ApiResource()]
#[ORM\HasLifecycleCallbacks] // *Nouveau* : pour déclencher la mise à jour du champ 'point'
#[ORM\Table(name: 'user_locations')]
#[ORM\Index(fields: ['point'], name: 'idx_user_locations_point', options: ['spatial' => true])]
class UserLocation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(type: 'datetimetz_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $recordedAt;

    #[ORM\ManyToOne(inversedBy: 'userLocations', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[Groups(['user:write'])]
    private float $latitude;

    #[Groups(['user:write'])]
    private float $longitude;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user:write'])]
    private ?string $geohash = null;

    #[ORM\Column(
        type: 'geography',
        options: ['geometry_type' => 'POINT', 'srid' => 4326],
        nullable: true
    )]
    private ?string $point = null;

    public function __construct()
    {
        $this->recordedAt = new DateTimeImmutable();
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

    public function getRecordedAt(): DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getGeohash(): ?string
    {
        return $this->geohash;
    }

    public function setGeohash(?string $geohash): self
    {
        $this->geohash = $geohash;
        return $this;
    }

    public function getPoint(): ?string
    {
        return $this->point;
    }

    public function setPoint(string $point): self
    {
        $this->point = $point;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatePointFromCoordinates(): void
    {
        // Si les coordonnées lat/lon sont définies, on met à jour le champ PostGIS (WKT)
        if ($this->latitude !== null && $this->longitude !== null) {
            // PostGIS utilise le format POINT(longitude latitude)
            $this->point = "POINT({$this->longitude} {$this->latitude})";
        }
    }
}
