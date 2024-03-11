<?php

namespace App\Entity;

use App\Repository\NotationBikerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotationBikerRepository::class)]
class NotationBiker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notationBikers')]
    private ?ControlMission $controlMission = null;

    #[ORM\ManyToOne(inversedBy: 'notationBikers')]
    private ?MissionSession $missionSession = null;

    #[ORM\Column]
    private ?float $note = null;
 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getControlMission(): ?ControlMission
    {
        return $this->controlMission;
    }

    public function setControlMission(?ControlMission $controlMission): static
    {
        $this->controlMission = $controlMission;

        return $this;
    }

    public function getMissionSession(): ?MissionSession
    {
        return $this->missionSession;
    }

    public function setMissionSession(?MissionSession $missionSession): static
    {
        $this->missionSession = $missionSession;

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
