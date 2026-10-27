<?php

namespace App\Entity;

use App\Enum\StatutCampagne;
use App\Repository\CampagneRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CampagneRepository::class)]
class Campagne
{
    /**
     * @var Collection<int, Tour>
     */
    #[ORM\OneToMany(targetEntity: Tour::class, mappedBy: 'campagne')]
    private Collection $tours;

    public function __construct()
    {
        $this->tours = new ArrayCollection();
    }

    /**
     * @return Collection<int, Tour>
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function ajouterTour(Tour $tour): static
    {
        if (!$this->tours->contains($tour)) {
            $this->tours->add($tour);
            $tour->setCampagne($this);
        }

        return $this;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $montantCotisation = null;

    #[ORM\Column]
    private ?int $nombreMembresMax = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column(enumType: StatutCampagne::class, length: 20)]
    private StatutCampagne $statut = StatutCampagne::EN_COURS;

    #[ORM\ManyToOne(targetEntity: TypeCampagne::class, inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCampagne $typeCampagne = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getMontantCotisation(): ?float
    {
        return $this->montantCotisation;
    }

    public function setMontantCotisation(float $montantCotisation): static
    {
        $this->montantCotisation = $montantCotisation;
        return $this;
    }

    public function getNombreMembresMax(): ?int
    {
        return $this->nombreMembresMax;
    }

    public function setNombreMembresMax(int $nombreMembresMax): static
    {
        $this->nombreMembresMax = $nombreMembresMax;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeImmutable $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getStatut(): StatutCampagne
    {
        return $this->statut;
    }

    public function getTypeCampagne(): ?TypeCampagne
    {
        return $this->typeCampagne;
    }

    public function setTypeCampagne(TypeCampagne $typeCampagne): static
    {
        $this->typeCampagne = $typeCampagne;
        return $this;
    }

    public function cloturer(): void
    {
        $this->statut = StatutCampagne::CLOTUREE;
    }
}


