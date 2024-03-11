<?php

namespace App\Entity;

use App\Repository\MissionSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionSessionRepository::class)]
class MissionSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_created = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'missionsavebiker', targetEntity: PointLocalisation::class)]
    private Collection $pointLocalisations;

    #[ORM\ManyToOne(inversedBy: 'missionSaves')]
    private ?ListMissionBiker $missionbiker = null;


    #[ORM\Column(nullable: true)]
    private ?bool $startMission = null;

    #[ORM\Column(nullable: true)]
    private ?bool $endMission = null;

    #[ORM\OneToMany(mappedBy: 'biker_mission', targetEntity: ControlMission::class)]
    private Collection $controlMissions;

    #[ORM\ManyToOne(inversedBy: 'missionSessions')]
    private ?Secteur $secteur = null;

    #[ORM\OneToMany(mappedBy: 'missionSession', targetEntity: NotationBiker::class)]
    private Collection $notationBikers;
    public function __construct()
    {

        $this->startMission = true;
        $this->endMission = false;
        $this->date_created = new \DateTime();

        $this->pointLocalisations = new ArrayCollection();
        $this->controlMissions = new ArrayCollection();
        $this->notationBikers = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): static
    {
        $this->date_created = $date_created;

        return $this;
    }


    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): static
    {
        $this->date_end = $date_end;

        return $this;
    }

    /**
     * @return Collection<int, PointLocalisation>
     */
    public function getPointLocalisations(): Collection
    {
        return $this->pointLocalisations;
    }

    public function addPointLocalisation(PointLocalisation $pointLocalisation): static
    {
        if (!$this->pointLocalisations->contains($pointLocalisation)) {
            $this->pointLocalisations->add($pointLocalisation);
            $pointLocalisation->setMissionsavebiker($this);
        }

        return $this;
    }

    public function removePointLocalisation(PointLocalisation $pointLocalisation): static
    {
        if ($this->pointLocalisations->removeElement($pointLocalisation)) {
            // set the owning side to null (unless already changed)
            if ($pointLocalisation->getMissionsavebiker() === $this) {
                $pointLocalisation->setMissionsavebiker(null);
            }
        }

        return $this;
    }

    public function getMissionbiker(): ?ListMissionBiker
    {
        return $this->missionbiker;
    }

    public function setMissionbiker(?ListMissionBiker $missionbiker): static
    {
        $this->missionbiker = $missionbiker;

        return $this;
    }

    public function getStartMission(): ?bool
    {
        return $this->startMission;
    }

    public function setStartMission(?bool $startMission): static
    {
        $this->startMission = $startMission;

        return $this;
    }

    public function isEndMission(): ?bool
    {
        return $this->endMission;
    }

    public function setEndMission(?bool $endMission): static
    {
        $this->endMission = $endMission;

        return $this;
    }


    public function getSecteur(): ?Secteur
    {
        return $this->secteur;
    }

    public function setSecteur(?Secteur $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * @return Collection<int, NotationBiker>
     */
    public function getNotationBikers(): Collection
    {
        return $this->notationBikers;
    }

    public function addNotationBiker(NotationBiker $notationBiker): static
    {
        if (!$this->notationBikers->contains($notationBiker)) {
            $this->notationBikers->add($notationBiker);
            $notationBiker->setMissionSession($this);
        }

        return $this;
    }

    public function removeNotationBiker(NotationBiker $notationBiker): static
    {
        if ($this->notationBikers->removeElement($notationBiker)) {
            // set the owning side to null (unless already changed)
            if ($notationBiker->getMissionSession() === $this) {
                $notationBiker->setMissionSession(null);
            }
        }

        return $this;
    }
}
