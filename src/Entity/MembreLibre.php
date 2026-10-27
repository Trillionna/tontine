<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class MembreLibre extends Membre
{
    #[ORM\Column]
    private ?float $montantMinimum = null;

    #[ORM\Column(length: 50)]
    private ?string $frequenceLibre = null;

    #[ORM\Column(type: 'text')]
    private ?string $justification = null;

    public function getMontantMinimum(): ?float
    {
        return $this->montantMinimum;
    }

    public function setMontantMinimum(float $montantMinimum): static
    {
        $this->montantMinimum = $montantMinimum;
        return $this;
    }

    public function getFrequenceLibre(): ?string
    {
        return $this->frequenceLibre;
    }

    public function setFrequenceLibre(string $frequenceLibre): static
    {
        $this->frequenceLibre = $frequenceLibre;
        return $this;
    }

    public function getJustification(): ?string
    {
        return $this->justification;
    }

    public function setJustification(string $justification): static
    {
        $this->justification = $justification;
        return $this;
    }

    public function definirMontant(float $montant): void
    {
        if ($montant < $this->montantMinimum) {
            throw new \InvalidArgumentException(
                'Le montant doit ?tre sup?rieur ou ?gal au montant minimum.'
            );
        }

        $this->montantMinimum = $montant;
    }

    public function calculerReste(): float
    {
        return 0.0;
    }
}
