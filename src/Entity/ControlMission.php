<?php

namespace App\Entity;

use App\Repository\ControlMissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $date_created;

    #[ORM\ManyToOne(inversedBy: 'controlMissions')]
    private ?Secteur $secteur = null;

    #[ORM\ManyToOne(inversedBy: 'controlMissions')]
    private ?Mission $mission = null;

    #[ORM\OneToMany(mappedBy: 'controlMission', targetEntity: NotationBiker::class)]
    private Collection $notationBikers;
    public function __construct()
    {
        $this->date_created = new \DateTime();

        $this->status = 0;
        $this->notationBikers = new ArrayCollection();
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

    public function isStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
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


    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

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

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): static
    {
        $this->mission = $mission;

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
            $notationBiker->setControlMission($this);
        }

        return $this;
    }

    public function removeNotationBiker(NotationBiker $notationBiker): static
    {
        if ($this->notationBikers->removeElement($notationBiker)) {
            // set the owning side to null (unless already changed)
            if ($notationBiker->getControlMission() === $this) {
                $notationBiker->setControlMission(null);
            }
        }

        return $this;
    }
}
