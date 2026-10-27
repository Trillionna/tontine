<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Cotisation extends Paiement
{
    #[ORM\Column]
    private ?float $montantAttendu = null;

    public function getMontantAttendu(): ?float
    {
        return $this->montantAttendu;
    }

    public function setMontantAttendu(float $montantAttendu): static
    {
        if ($montantAttendu < 0) {
            throw new \InvalidArgumentException('Le montant attendu ne peut pas ?tre n?gatif.');
        }

        $this->montantAttendu = $montantAttendu;
        return $this;
    }

    public function estComplete(): bool
    {
        return $this->montant !== null
            && $this->montantAttendu !== null
            && $this->montant >= $this->montantAttendu;
    }
}
