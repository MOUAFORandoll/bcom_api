<?php

namespace App\Entity;

use App\Repository\ObjectFileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectFileRepository::class)]
class ObjectFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $src = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_created = null;

    #[ORM\OneToMany(mappedBy: 'cniAvant', targetEntity: InfoBiker::class)]
    private Collection $infoBikers;


    public function __construct()
    {
        $this->date_created = new \DateTime();
        $this->infoBikers = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): static
    {
        $this->src = $src;

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

    /**
     * @return Collection<int, InfoBiker>
     */
    public function getInfoBikers(): Collection
    {
        return $this->infoBikers;
    }

    public function addInfoBiker(InfoBiker $infoBiker): static
    {
        if (!$this->infoBikers->contains($infoBiker)) {
            $this->infoBikers->add($infoBiker);
            $infoBiker->setCniAvant($this);
        }

        return $this;
    }

    public function removeInfoBiker(InfoBiker $infoBiker): static
    {
        if ($this->infoBikers->removeElement($infoBiker)) {
            // set the owning side to null (unless already changed)
            if ($infoBiker->getCniAvant() === $this) {
                $infoBiker->setCniAvant(null);
            }
        }

        return $this;
    }
}
