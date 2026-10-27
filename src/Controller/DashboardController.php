<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Entity\Cotisation;
use App\Entity\Membre;
use App\Entity\Paiement;
use App\Entity\Participation;
use App\Entity\Tour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $stats = [
            'membres' => $em->getRepository(Membre::class)->count([]),
            'campagnes' => $em->getRepository(Campagne::class)->count([]),
            'participations' => $em->getRepository(Participation::class)->count([]),
            'cotisations' => $em->getRepository(Cotisation::class)->count([]),
            'paiements' => $em->getRepository(Paiement::class)->count([]),
            'tours' => $em->getRepository(Tour::class)->count([]),
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}