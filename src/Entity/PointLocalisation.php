<?php

namespace App\Entity;

use App\Repository\PointLocalisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PointLocalisationRepository::class)]
#[ApiResource(collectionOperations: [
    'get' => [
        'normalization_context' => [
            'groups' => ['read:pointLivraison']
        ],

    ],
])]
class PointLocalisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["read:pointLivraison"])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $date_created;

    #[ORM\ManyToOne(inversedBy: 'pointLocalisations')]
    private ?MissionSession $missionsavebiker = null;
    public function __construct()
    {

        $this->date_created = new \DateTime();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

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

    public function getMissionsavebiker(): ?MissionSession
    {
        return $this->missionsavebiker;
    }

    public function setMissionsavebiker(?MissionSession $missionsavebiker): static
    {
        $this->missionsavebiker = $missionsavebiker;

        return $this;
    }
}
