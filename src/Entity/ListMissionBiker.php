<?php

namespace App\Entity;

use App\Repository\ListMissionBikerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListMissionBikerRepository::class)]
class ListMissionBiker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'listMissionBikers')]
    private ?Mission $mission = null;

    #[ORM\ManyToOne(inversedBy: 'listMissionBikers')]
    private ?UserPlateform $biker = null;

    #[ORM\Column(type: "date")]
    private $date_created;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'missionbiker', targetEntity: MissionSession::class)]
    private Collection $missionSaves;
    public function __construct()
    {

        $this->date_created = new \DateTime();

        $this->status = false;
        $this->missionSaves = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getBiker(): ?UserPlateform
    {
        return $this->biker;
    }

    public function setBiker(?UserPlateform $biker): static
    {
        $this->biker = $biker;

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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, MissionSession>
     */
    public function getMissionSessions(): Collection
    {
        return $this->missionSaves;
    }

    public function addMissionSession(MissionSession $missionSave): static
    {
        if (!$this->missionSaves->contains($missionSave)) {
            $this->missionSaves->add($missionSave);
            $missionSave->setMissionbiker($this);
        }

        return $this;
    }

    public function removeMissionSession(MissionSession $missionSave): static
    {
        if ($this->missionSaves->removeElement($missionSave)) {
            // set the owning side to null (unless already changed)
            if ($missionSave->getMissionbiker() === $this) {
                $missionSave->setMissionbiker(null);
            }
        }

        return $this;
    }
}
