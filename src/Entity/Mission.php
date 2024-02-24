<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ApiResource]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: ListMissionBiker::class)]
    private Collection $listMissionBikers;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_created = null;

    #[ORM\Column(length: 255)]
    private ?string $nbre_point = null;

    public function __construct()
    {
        $this->status = true;

        $this->date_created = new \DateTime();
        $this->listMissionBikers = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
     * @return Collection<int, ListMissionBiker>
     */
    public function getListMissionBikers(): Collection
    {
        return $this->listMissionBikers;
    }

    public function addListMissionBiker(ListMissionBiker $listMissionBiker): static
    {
        if (!$this->listMissionBikers->contains($listMissionBiker)) {
            $this->listMissionBikers->add($listMissionBiker);
            $listMissionBiker->setMission($this);
        }

        return $this;
    }

    public function removeListMissionBiker(ListMissionBiker $listMissionBiker): static
    {
        if ($this->listMissionBikers->removeElement($listMissionBiker)) {
            // set the owning side to null (unless already changed)
            if ($listMissionBiker->getMission() === $this) {
                $listMissionBiker->setMission(null);
            }
        }

        return $this;
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

    public function getNbrePoint(): ?string
    {
        return $this->nbre_point;
    }

    public function setNbrePoint(string $nbre_point): static
    {
        $this->nbre_point = $nbre_point;

        return $this;
    }
}
