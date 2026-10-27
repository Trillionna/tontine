<?php

namespace App\Entity;

use App\Enum\StatutPaiement;
use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type_paiement', type: 'string')]
#[ORM\DiscriminatorMap([
    'cotisation' => Cotisation::class,
    'libre' => PaiementLibre::class
])]
abstract class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column]
    protected ?float $montant = null;

    #[ORM\Column]
    protected ?\DateTimeImmutable $datePaiement = null;

    #[ORM\Column(length: 50)]
    protected ?string $modePaiement = null;

    #[ORM\Column(enumType: StatutPaiement::class, length: 20)]
    protected StatutPaiement $statut = StatutPaiement::EN_ATTENTE;

    #[ORM\ManyToOne(targetEntity: Participation::class, inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Participation $participation = null;

    #[ORM\ManyToOne(targetEntity: Tour::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Tour $tour = null;

    public function __construct()
    {
        $this->datePaiement = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException('Le montant doit ?tre strictement positif.');
        }

        $this->montant = $montant;
        return $this;
    }

    public function getDatePaiement(): ?\DateTimeImmutable
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeImmutable $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): static
    {
        $this->modePaiement = $modePaiement;
        return $this;
    }

    public function getStatut(): StatutPaiement
    {
        return $this->statut;
    }

    public function valider(): void
    {
        $this->statut = StatutPaiement::VALIDE;
    }

    public function annuler(): void
    {
        $this->statut = StatutPaiement::ANNULE;
    }

    public function getParticipation(): ?Participation
    {
        return $this->participation;
    }

    public function setParticipation(Participation $participation): static
    {
        $this->participation = $participation;
        return $this;
    }

    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    public function setTour(Tour $tour): static
    {
        $this->tour = $tour;
        return $this;
    }
}
