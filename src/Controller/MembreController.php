<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Enum\StatutMembre;
use App\Form\MembreType;
use App\Repository\MembreRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/membres', name: 'app_membre_')]
class MembreController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, MembreRepository $repository): Response
    {
        $recherche = trim((string) $request->query->get('q', ''));
        $statut = (string) $request->query->get('statut', '');

        $qb = $repository->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC');

        if ($recherche !== '') {
            $qb->andWhere(
                'LOWER(m.nom) LIKE :q OR LOWER(m.prenom) LIKE :q OR LOWER(m.email) LIKE :q OR m.telephone LIKE :q'
            )->setParameter('q', '%' . mb_strtolower($recherche) . '%');
        }

        if ($statut !== '') {
            $statutValide = StatutMembre::tryFrom($statut);
            if ($statutValide !== null) {
                $qb->andWhere('m.statut = :statut')
                    ->setParameter('statut', $statutValide);
            }
        }

        return $this->render('membre/index.html.twig', [
            'membres' => $qb->getQuery()->getResult(),
            'recherche' => $recherche,
            'statutFiltre' => $statut,
            'total' => $repository->count([]),
            'actifs' => $repository->count(['statut' => StatutMembre::ACTIF]),
            'attente' => $repository->count(['statut' => StatutMembre::EN_ATTENTE]),
            'suspendus' => $repository->count(['statut' => StatutMembre::SUSPENDU]),
        ]);
    }

    #[Route('/nouveau', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $membre = new Membre();
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($membre);
                $entityManager->flush();

                $this->addFlash('success', 'Le membre a été ajouté avec succès.');

                return $this->redirectToRoute('app_membre_index');
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('error', 'Cette adresse e-mail est déjà utilisée.');
            }
        }

        return $this->render('membre/form.html.twig', [
            'form' => $form,
            'titre' => 'Ajouter un membre',
            'description' => 'Enregistrer un nouveau membre dans la tontine.',
            'membre' => $membre,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Membre $membre, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                $this->addFlash('success', 'Les informations du membre ont été mises à jour.');

                return $this->redirectToRoute('app_membre_index');
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('error', 'Cette adresse e-mail est déjà utilisée par un autre membre.');
            }
        }

        return $this->render('membre/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier un membre',
            'description' => 'Mettre à jour les informations du membre.',
            'membre' => $membre,
        ]);
    }

    #[Route('/{id}/statut/{statut}', name: 'statut', requirements: ['id' => '\d+', 'statut' => 'actif|suspendu'], methods: ['POST'])]
    public function changerStatut(
        Membre $membre,
        string $statut,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('statut_membre_' . $membre->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton de sécurité invalide.');
        }

        if ($statut === 'actif') {
            $membre->valider();
        } else {
            $membre->suspendre();
        }

        $entityManager->flush();
        $this->addFlash('success', 'Le statut du membre a été mis à jour.');

        return $this->redirectToRoute('app_membre_index');
    }

    #[Route('/{id}/supprimer', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(
        Membre $membre,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('supprimer_membre_' . $membre->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton de sécurité invalide.');
        }

        try {
            $entityManager->remove($membre);
            $entityManager->flush();
            $this->addFlash('success', 'Le membre a été supprimé.');
        } catch (\Throwable $exception) {
            $this->addFlash('error', 'Suppression impossible : ce membre est peut-être lié à des participations ou à des paiements.');
        }

        return $this->redirectToRoute('app_membre_index');
    }
}
