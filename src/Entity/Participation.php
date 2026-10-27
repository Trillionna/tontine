<?php

namespace App\Entity;

use App\Enum\StatutPaiement;
use App\Repository\ParticipationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(
    name: 'uniq_participation_membre_campagne',
    columns: ['membre_id', 'campagne_id']
)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAdhesion = null;

    #[ORM\Column]
    private int $ordrePassage = 1;

    #[ORM\Column]
    private bool $aBeneficie = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Membre $membre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campagne $campagne = null;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(mappedBy: 'participation', targetEntity: Paiement::class)]
    private Collection $paiements;

    /**
     * @var Collection<int, Tour>
     */
    #[ORM\OneToMany(mappedBy: 'beneficiaire', targetEntity: Tour::class)]
    private Collection $toursBeneficiaire;

    public function __construct()
    {
        $this->dateAdhesion = new \DateTimeImmutable();
        $this->paiements = new ArrayCollection();
        $this->toursBeneficiaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAdhesion(): ?\DateTimeImmutable
    {
        return $this->dateAdhesion;
    }

    public function getOrdrePassage(): int
    {
        return $this->ordrePassage;
    }

    public function setOrdrePassage(int $ordrePassage): static
    {
        if ($ordrePassage < 1) {
            throw new \InvalidArgumentException('L’ordre de passage doit être positif.');
        }

        $this->ordrePassage = $ordrePassage;

        return $this;
    }

    public function aBeneficie(): bool
    {
        return $this->aBeneficie;
    }

    public function marquerCommeBeneficiaire(): void
    {
        $this->aBeneficie = true;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(Membre $membre): static
    {
        $this->membre = $membre;

        return $this;
    }

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(Campagne $campagne): static
    {
        $this->campagne = $campagne;

        return $this;
    }

    /** @return Collection<int, Paiement> */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    /** @return Collection<int, Tour> */
    public function getToursBeneficiaire(): Collection
    {
        return $this->toursBeneficiaire;
    }

    public function totalVerse(): float
    {
        $total = 0.0;

        foreach ($this->paiements as $paiement) {
            if (
                $paiement->getStatut() === StatutPaiement::VALIDE
                && $paiement->getMontant() !== null
            ) {
                $total += $paiement->getMontant();
            }
        }

        return $total;
    }

    public function estAJour(): bool
    {
        return $this->campagne !== null
            && $this->totalVerse() >= $this->campagne->getMontantCotisation();
    }
}
