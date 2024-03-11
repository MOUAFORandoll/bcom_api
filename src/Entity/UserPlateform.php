<?php

namespace App\Entity;


use App\Repository\UserPlateformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UserCreateController;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\FunctionU\MyFunction;


#[ORM\Entity(repositoryClass: UserPlateformRepository::class)]
#[ApiResource(
    itemOperations: [
        'get' => [],
        'patch' => [
            'denormalization_context' => [
                'groups' => ['create:user']
            ],
            'controller' => UserCreateController::class
        ],
        'delete' => []
    ],
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:user']
            ],
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')"
        ],
        'post' => [
            'denormalization_context' => [
                'groups' => ['create:user']
            ],
            'controller' => UserCreateController::class
        ]
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',

        'nom' => 'exact',
        'phone' => 'exact',
        'email' => 'exact',
        'typeUser' => 'exact'
    ]
)]
class UserPlateform implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["create:user", "read:user"])]
    private $nom;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["create:user", "read:user"])]
    private $prenom;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["create:user", "read:user"])]
    private $email;

    #[ORM\Column(type: "json")]
    private $roles = ['ROLE_USER'];

    #[ORM\Column(type: "integer", length: 255, unique: true)]
    #[Groups(["create:user", "read:user"])]
    private $phone;

    #[ORM\Column(type: "boolean")]
    #[Groups(["create:user", "read:user"])]
    private $status = true;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["create:user"])]
    private $password;

    #[ORM\ManyToOne(targetEntity: TypeUser::class, inversedBy: "users")]
    #[Groups(["create:user"])]
    private $typeUser;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $date_created;


    #[ORM\Column(type: "string", length: 10000000000, nullable: true)]
    private $keySecret;


    #[ORM\Column(type: "string",   nullable: true)]
    #[Groups(["create:user", "read:user"])]
    private $codeParrainage;


    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $codeRecup;

    #[ORM\OneToMany(mappedBy: 'biker', targetEntity: ListMissionBiker::class)]
    private Collection $listMissionBikers;

    #[ORM\OneToMany(mappedBy: 'CTerrain', targetEntity: ControlMission::class)]
    private Collection $controlMissions;

    #[ORM\OneToMany(mappedBy: 'user_plateform', targetEntity: UserObject::class)]
    private Collection $userObjects;

    #[ORM\OneToMany(mappedBy: 'biker', targetEntity: InfoBiker::class)]
    private Collection $infoBikers;

    #[ORM\OneToMany(mappedBy: 'biker', targetEntity: DisponibiliteBiker::class)]
    private Collection $disponibiliteBikers;

    public function __construct()
    {

        $this->date_created = new \DateTime();

        $this->status = false;
        $this->listMissionBikers = new ArrayCollection();
        $this->controlMissions = new ArrayCollection();
        $this->infoBikers = new ArrayCollection();
        $this->disponibiliteBikers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function getNomComplet()
    {
        return $this->getNom() . ' ' . $this->getPreNom();
    }

    public function getProfile()
    {
        //     return count($this->getUserObjects())  == 0 ? '' : new MyFunction()->getBackendUrl() . '/images/users/' . $this->getUserObjects()->first()->getSrc();
    }

    /**
     * @return Collection<int, UserObject>
     */
    public function getUserObjects(): Collection
    {
        return $this->userObjects;
    }

    public function addUserObject(UserObject $userObject): static
    {
        if (!$this->userObjects->contains($userObject)) {
            $this->userObjects->add($userObject);
            $userObject->setUserPlateform($this);
        }

        return $this;
    }

    public function removeUserObject(UserObject $userObject): static
    {
        if ($this->userObjects->removeElement($userObject)) {
            // set the owning side to null (unless already changed)
            if ($userObject->getUserPlateform() === $this) {
                $userObject->setUserPlateform(null);
            }
        }

        return $this;
    }
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function   getUserIdentifier(): string
    {
        return (string) $this->phone;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->phone;
    }

    public function getTypeUser(): ?TypeUser
    {
        return $this->typeUser;
    }

    public function setTypeUser(?TypeUser $typeUser): self
    {
        $this->typeUser = $typeUser;

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

    public function getKeySecret(): ?string
    {
        return $this->keySecret;
    }

    public function setKeySecret(string $keySecret): self
    {
        $this->keySecret = $keySecret;

        return $this;
    }

    public function getCodeParrainage(): ?string
    {
        return $this->codeParrainage;
    }

    public function setCodeParrainage(string $codeParrainage): self
    {
        $this->codeParrainage = $codeParrainage;

        return $this;
    }

    public function getCodeRecup(): ?string
    {
        return $this->codeRecup;
    }

    public function setCodeRecup(string $codeRecup): self
    {
        $this->codeRecup = $codeRecup;

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
            $listMissionBiker->setBiker($this);
        }

        return $this;
    }

    public function removeListMissionBiker(ListMissionBiker $listMissionBiker): static
    {
        if ($this->listMissionBikers->removeElement($listMissionBiker)) {
            // set the owning side to null (unless already changed)
            if ($listMissionBiker->getBiker() === $this) {
                $listMissionBiker->setBiker(null);
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
            $controlMission->setCTerrain($this);
        }

        return $this;
    }

    public function removeControlMission(ControlMission $controlMission): static
    {
        if ($this->controlMissions->removeElement($controlMission)) {
            // set the owning side to null (unless already changed)
            if ($controlMission->getCTerrain() === $this) {
                $controlMission->setCTerrain(null);
            }
        }

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
            $infoBiker->setBiker($this);
        }

        return $this;
    }

    public function removeInfoBiker(InfoBiker $infoBiker): static
    {
        if ($this->infoBikers->removeElement($infoBiker)) {
            // set the owning side to null (unless already changed)
            if ($infoBiker->getBiker() === $this) {
                $infoBiker->setBiker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DisponibiliteBiker>
     */
    public function getDisponibiliteBikers(): Collection
    {
        return $this->disponibiliteBikers;
    }

    public function addDisponibiliteBiker(DisponibiliteBiker $disponibiliteBiker): static
    {
        if (!$this->disponibiliteBikers->contains($disponibiliteBiker)) {
            $this->disponibiliteBikers->add($disponibiliteBiker);
            $disponibiliteBiker->setBiker($this);
        }

        return $this;
    }

    public function removeDisponibiliteBiker(DisponibiliteBiker $disponibiliteBiker): static
    {
        if ($this->disponibiliteBikers->removeElement($disponibiliteBiker)) {
            // set the owning side to null (unless already changed)
            if ($disponibiliteBiker->getBiker() === $this) {
                $disponibiliteBiker->setBiker(null);
            }
        }

        return $this;
    } // Dans UserPlateform

    /**
     * Récupère le statut de la dernière disponibilité du biker.
     * 
     * @return bool Le statut de la dernière disponibilité, ou false si aucune disponibilité n'existe.
     */
    public function getLastDisponibiliteStatus(): bool
    {
        if ($this->disponibiliteBikers->isEmpty()) {
            return false;
        }

        // Convertir la collection en tableau
        $disponibilites = $this->disponibiliteBikers->toArray();

        // Trier le tableau par date de début décroissante (vous devrez peut-être ajuster ce code si votre entité utilise un champ différent pour la date)
        usort($disponibilites, function ($a, $b) {
            return $b->getStart() <=> $a->getStart();
        });

        // Retourner le statut de la première disponibilité dans le tableau trié
        return $disponibilites[0]->isStatus();
    }
}
