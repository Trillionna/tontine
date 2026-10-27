<?php

namespace App\Entity;

use App\Repository\TourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $numero;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateTour = null;

    #[ORM\Column]
    private float $montantCagnotte = 0.0;

    #[ORM\Column]
    private bool $estCloture = false;

    #[ORM\ManyToOne(targetEntity: Campagne::class, inversedBy: 'tours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campagne $campagne = null;

    #[ORM\ManyToOne(targetEntity: Participation::class, inversedBy: 'toursBeneficiaire')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Participation $beneficiaire = null;

    public function getBeneficiaire(): ?Participation
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?Participation $beneficiaire): static
    {
        $this->beneficiaire = $beneficiaire;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        if ($numero < 1) {
            throw new \InvalidArgumentException('Le num?ro du tour doit ?tre positif.');
        }

        $this->numero = $numero;
        return $this;
    }

    public function getDateTour(): ?\DateTimeImmutable
    {
        return $this->dateTour;
    }

    public function setDateTour(\DateTimeImmutable $dateTour): static
    {
        $this->dateTour = $dateTour;
        return $this;
    }

    public function getMontantCagnotte(): float
    {
        return $this->montantCagnotte;
    }

    public function setMontantCagnotte(float $montantCagnotte): static
    {
        if ($montantCagnotte < 0) {
            throw new \InvalidArgumentException('La cagnotte ne peut pas ?tre n?gative.');
        }

        $this->montantCagnotte = $montantCagnotte;
        return $this;
    }

    public function getEstCloture(): bool
    {
        return $this->estCloture;
    }

    public function setEstCloture(bool $estCloture): static
    {
        $this->estCloture = $estCloture;
        return $this;
    }

    public function isCloture(): bool
    {
        return $this->estCloture;
    }

    public function cloturerTour(): void
    {
        $this->estCloture = true;
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
}
