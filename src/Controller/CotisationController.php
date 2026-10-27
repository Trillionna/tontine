<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Form\CotisationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cotisation')]
final class CotisationController extends AbstractController
{
    #[Route(name: 'app_cotisation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $cotisations = $entityManager
            ->getRepository(Cotisation::class)
            ->findAll();

        return $this->render('cotisation/index.html.twig', [
            'cotisations' => $cotisations,
        ]);
    }

    #[Route('/new', name: 'app_cotisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cotisation = new Cotisation();
        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cotisation);
            $entityManager->flush();

            return $this->redirectToRoute('app_cotisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisation/new.html.twig', [
            'cotisation' => $cotisation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cotisation_show', methods: ['GET'])]
    public function show(Cotisation $cotisation): Response
    {
        return $this->render('cotisation/show.html.twig', [
            'cotisation' => $cotisation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisation $cotisation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cotisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisation/edit.html.twig', [
            'cotisation' => $cotisation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cotisation_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisation $cotisation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cotisation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cotisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisation_index', [], Response::HTTP_SEE_OTHER);
    }
}
