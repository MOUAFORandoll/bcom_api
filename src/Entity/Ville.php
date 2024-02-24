<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ApiResource(collectionOperations: [
    'get' => [
        'normalization_context' => [
            'groups' => ['read:ville']
        ],

    ],
])]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups([
        "read:ville"
    ])]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["read:ville"])]
    #[ORM\Column(length: 500)]
    private ?string $libelle = null;

    

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: PointLocalisation::class)]
    private Collection $pointLivraisons;

    public function __construct()
    {
         $this->pointLivraisons = new ArrayCollection();
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
 

    /**
     * @return Collection<int, PointLocalisation>
     */
    public function getPointLocalisations(): Collection
    {
        return $this->pointLivraisons;
    }

    public function addPointLocalisation(PointLocalisation $pointLivraison): static
    {
        if (!$this->pointLivraisons->contains($pointLivraison)) {
            $this->pointLivraisons->add($pointLivraison);
            $pointLivraison->setVille($this);
        }

        return $this;
    }

    public function removePointLocalisation(PointLocalisation $pointLivraison): static
    {
        if ($this->pointLivraisons->removeElement($pointLivraison)) {
            // set the owning side to null (unless already changed)
            if ($pointLivraison->getVille() === $this) {
                $pointLivraison->setVille(null);
            }
        }

        return $this;
    }
}
