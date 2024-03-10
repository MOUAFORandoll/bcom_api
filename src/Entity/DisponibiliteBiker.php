<?php

namespace App\Entity;

use App\Repository\DisponibiliteBikerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisponibiliteBikerRepository::class)]
class DisponibiliteBiker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start_dispo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_dispo = null;

    #[ORM\ManyToOne(inversedBy: 'disponibiliteBikers')]
    private ?UserPlateform $biker = null;

    #[ORM\Column(nullable: true)]
    private ?float $long = null;

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;
    public function __construct()
    {
        $this->status = true;

        $this->start_dispo = new \DateTime();;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start_dispo;
    }

    public function setStart(\DateTimeInterface $start_dispo): static
    {
        $this->start_dispo = $start_dispo;

        return $this;
    }

    public function getEndDispo(): ?\DateTimeInterface
    {
        return $this->end_dispo;
    }

    public function setEndDispo(?\DateTimeInterface $end_dispo): static
    {
        $this->end_dispo = $end_dispo;

        return $this;
    }

    public function getBiker(): ?UserPlateform
    {
        return $this->biker;
    }

    public function setBiker(?UserPlateform $biker): static
    {
        $this->biker = $biker;

        return $this;
    }

    public function getLong(): ?float
    {
        return $this->long;
    }

    public function setLong(float $long): static
    {
        $this->long = $long;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }
}
