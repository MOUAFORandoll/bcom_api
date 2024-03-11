<?php

namespace App\Entity;

use App\Repository\SecteurRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecteurRepository::class)]
class Secteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\OneToMany(mappedBy: 'secteur', targetEntity: MissionSession::class)]
    private Collection $missionSessions;

    #[ORM\OneToMany(mappedBy: 'secteur', targetEntity: ControlMission::class)]
    private Collection $controlMissions;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->missionSessions = new ArrayCollection();
        $this->controlMissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return Collection<int, MissionSession>
     */
    public function getMissionSessions(): Collection
    {
        return $this->missionSessions;
    }

    public function addMissionSession(MissionSession $missionSession): static
    {
        if (!$this->missionSessions->contains($missionSession)) {
            $this->missionSessions->add($missionSession);
            $missionSession->setSecteur($this);
        }

        return $this;
    }

    public function removeMissionSession(MissionSession $missionSession): static
    {
        if ($this->missionSessions->removeElement($missionSession)) {
            // set the owning side to null (unless already changed)
            if ($missionSession->getSecteur() === $this) {
                $missionSession->setSecteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ControlMission>
     */
    public function getControlMissions(): Collection
    {
        return $this->controlMissions;
    }

    public function addControlMission(ControlMission $controlMission): static
    {
        if (!$this->controlMissions->contains($controlMission)) {
            $this->controlMissions->add($controlMission);
            $controlMission->setSecteur($this);
        }

        return $this;
    }

    public function removeControlMission(ControlMission $controlMission): static
    {
        if ($this->controlMissions->removeElement($controlMission)) {
            // set the owning side to null (unless already changed)
            if ($controlMission->getSecteur() === $this) {
                $controlMission->setSecteur(null);
            }
        }

        return $this;
    }
}
