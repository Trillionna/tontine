<?php

namespace App\Entity;

use App\Enum\StatutMembre;
use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type_membre', type: 'string')]
#[ORM\DiscriminatorMap(['membre' => Membre::class, 'libre' => MembreLibre::class])]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\Column(enumType: StatutMembre::class, length: 20)]
    private StatutMembre $statut = StatutMembre::EN_ATTENTE;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'membres')]
    #[ORM\JoinTable(name: 'membre_role')]
    private Collection $roles;

    public function __construct()
    {
        $this->dateInscription = new \DateTimeImmutable();
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->dateInscription;
    }

    public function getStatut(): StatutMembre
    {
        return $this->statut;
    }

    public function valider(): void
    {
        $this->statut = StatutMembre::ACTIF;
    }

    public function suspendre(): void
    {
        $this->statut = StatutMembre::SUSPENDU;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function ajouterRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->ajouterMembre($this);
        }

        return $this;
    }

    public function retirerRole(Role $role): static
    {
        if ($this->roles->removeElement($role)) {
            $role->retirerMembre($this);
        }

        return $this;
    }

    public function aRole(string $code): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getCode() === $code) {
                return true;
            }
        }

        return false;
    }
}

