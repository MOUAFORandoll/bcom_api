<?php

namespace App\Entity;

use App\Repository\ControlMissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ControlMissionRepository::class)]
class ControlMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'controlMissions')]
    private ?UserPlateform $CTerrain = null;


    #[ORM\ManyToOne(inversedBy: 'controlMissions')]
    private ?UserPlateform $CBureau = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\ManyToOne(inversedBy: 'controlMissions')]
    private ?MissionSession $biker_mission = null;

    #[ORM\Column(nullable: true)]
    private ?float $note = null;
    public function __construct()
    {



        $this->status = true;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCTerrain(): ?UserPlateform
    {
        return $this->CTerrain;
    }

    public function setCTerrain(?UserPlateform $CTerrain): static
    {
        $this->CTerrain = $CTerrain;

        return $this;
    }


    public function getCBureau(): ?UserPlateform
    {
        return $this->CBureau;
    }

    public function setCBureau(?UserPlateform $CBureau): static
    {
        $this->CBureau = $CBureau;

        return $this;
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

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getBikerMission(): ?MissionSession
    {
        return $this->biker_mission;
    }

    public function setBikerMission(?MissionSession $biker_mission): static
    {
        $this->biker_mission = $biker_mission;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): static
    {
        $this->note = $note;

        return $this;
    }
}
