<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PaiementLibre extends Paiement
{
    #[ORM\Column]
    private ?float $montantConvenu = null;

    #[ORM\Column]
    private float $soldeRestant = 0.0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $motifAjustement = null;

    public function getMontantConvenu(): ?float
    {
        return $this->montantConvenu;
    }

    public function setMontantConvenu(float $montantConvenu): static
    {
        if ($montantConvenu < 0) {
            throw new \InvalidArgumentException('Le montant convenu ne peut pas ?tre n?gatif.');
        }

        $this->montantConvenu = $montantConvenu;
        $this->soldeRestant = max(0, $montantConvenu - ($this->montant ?? 0));

        return $this;
    }

    public function getSoldeRestant(): float
    {
        return $this->soldeRestant;
    }

    public function getMotifAjustement(): ?string
    {
        return $this->motifAjustement;
    }

    public function ajusterMontant(float $montant): void
    {
        if ($montant < 0) {
            throw new \InvalidArgumentException('Le montant ne peut pas ?tre n?gatif.');
        }

        $this->montantConvenu = $montant;
        $this->soldeRestant = max(0, $montant - ($this->montant ?? 0));
    }

    public function setMotifAjustement(?string $motifAjustement): static
    {
        $this->motifAjustement = $motifAjustement;
        return $this;
    }
}
