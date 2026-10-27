<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Paiement;
use App\Entity\PaiementLibre;
use App\Form\CotisationType;
use App\Form\PaiementLibreType;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/paiement')]
final class PaiementController extends AbstractController
{
    #[Route(name: 'app_paiement_index', methods: ['GET'])]
    public function index(PaiementRepository $paiementRepository): Response
    {
        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_paiement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paiement = new PaiementLibre();
        $form = $this->createForm(PaiementLibreType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paiement);
            $entityManager->flush();

            $this->addFlash('success', 'Le paiement a été enregistré.');

            return $this->redirectToRoute(
                'app_paiement_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('paiement/new.html.twig', [
            'paiement' => $paiement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paiement_show', methods: ['GET'])]
    public function show(Paiement $paiement): Response
    {
        return $this->render('paiement/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paiement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Paiement $paiement,
        EntityManagerInterface $entityManager
    ): Response {
        $formType = $paiement instanceof Cotisation
            ? CotisationType::class
            : PaiementLibreType::class;

        $form = $this->createForm($formType, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le paiement a été modifié.');

            return $this->redirectToRoute(
                'app_paiement_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('paiement/edit.html.twig', [
            'paiement' => $paiement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paiement_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Paiement $paiement,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $paiement->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $entityManager->remove($paiement);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_paiement_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}