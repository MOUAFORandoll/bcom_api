<?php

namespace App\Entity;

use App\Repository\InfoBikerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoBikerRepository::class)]
class InfoBiker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    private ?string $cni = null;

    #[ORM\Column]
    private ?bool $handicap = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $handicap_description = null;

    #[ORM\Column]
    private ?bool $isBiker = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $isBikerYes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $isBikerNo = null;

    #[ORM\Column]
    private ?bool $isSyndicat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $isSyndicatYes = null;

    #[ORM\Column]
    private ?bool $haveMoto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $num_carte_grise_moto = null;

    #[ORM\Column]
    private ?int $bike_work_time = null;
    #[ORM\Column]
    private ?int $age = null;

    #[ORM\ManyToOne(inversedBy: 'infoBikers')]
    private ?UserPlateform $biker = null;

    #[ORM\ManyToOne(inversedBy: 'infoBikers')]
    private ?ObjectFile $cniAvant = null;

    #[ORM\ManyToOne(inversedBy: 'infoBikers')]
    private ?ObjectFile $cniArriere = null;

    #[ORM\ManyToOne(inversedBy: 'infoBikers')]
    private ?ObjectFile $carteGrise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): static
    {
        $this->cni = $cni;

        return $this;
    }

    public function isHandicap(): ?bool
    {
        return $this->handicap;
    }

    public function setHandicap(bool $handicap): static
    {
        $this->handicap = $handicap;

        return $this;
    }

    public function getHandicapDescription(): ?string
    {
        return $this->handicap_description;
    }

    public function setHandicapDescription(?string $handicap_description): static
    {
        $this->handicap_description = $handicap_description;

        return $this;
    }

    public function isIsBiker(): ?bool
    {
        return $this->isBiker;
    }

    public function setIsBiker(bool $isBiker): static
    {
        $this->isBiker = $isBiker;

        return $this;
    }

    public function getIsBikerYes(): ?string
    {
        return $this->isBikerYes;
    }

    public function setIsBikerYes(?string $isBikerYes): static
    {
        $this->isBikerYes = $isBikerYes;

        return $this;
    }

    public function getIsBikerNo(): ?string
    {
        return $this->isBikerNo;
    }

    public function setIsBikerNo(?string $isBikerNo): static
    {
        $this->isBikerNo = $isBikerNo;

        return $this;
    }

    public function isIsSyndicat(): ?bool
    {
        return $this->isSyndicat;
    }

    public function setIsSyndicat(bool $isSyndicat): static
    {
        $this->isSyndicat = $isSyndicat;

        return $this;
    }

    public function getIsSyndicatYes(): ?string
    {
        return $this->isSyndicatYes;
    }

    public function setIsSyndicatYes(?string $isSyndicatYes): static
    {
        $this->isSyndicatYes = $isSyndicatYes;

        return $this;
    }

    public function isHaveMoto(): ?bool
    {
        return $this->haveMoto;
    }

    public function setHaveMoto(bool $haveMoto): static
    {
        $this->haveMoto = $haveMoto;

        return $this;
    }

    public function getNumCarteGriseMoto(): ?string
    {
        return $this->num_carte_grise_moto;
    }

    public function setNumCarteGriseMoto(?string $num_carte_grise_moto): static
    {
        $this->num_carte_grise_moto = $num_carte_grise_moto;

        return $this;
    }

    public function getBikeWorkTime(): ?int
    {
        return $this->bike_work_time;
    }

    public function setBikeWorkTime(int $bike_work_time): static
    {
        $this->bike_work_time = $bike_work_time;

        return $this;
    }
    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

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

    public function getCniAvant(): ?ObjectFile
    {
        return $this->cniAvant;
    }

    public function setCniAvant(?ObjectFile $cniAvant): static
    {
        $this->cniAvant = $cniAvant;

        return $this;
    }

    public function getCniArriere(): ?ObjectFile
    {
        return $this->cniArriere;
    }

    public function setCniArriere(?ObjectFile $cniArriere): static
    {
        $this->cniArriere = $cniArriere;

        return $this;
    }

    public function getCarteGrise(): ?ObjectFile
    {
        return $this->carteGrise;
    }

    public function setCarteGrise(?ObjectFile $carteGrise): static
    {
        $this->carteGrise = $carteGrise;

        return $this;
    }
}
