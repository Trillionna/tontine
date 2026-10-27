<?php

require dirname(__DIR__) . '/vendor/autoload.php';

if (is_file(dirname(__DIR__) . '/.env')) {
    (new Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

use App\Entity\Role;
use App\Entity\Membre;
use App\Entity\TypeCampagne;
use App\Entity\Campagne;
use App\Entity\Participation;
use App\Entity\Tour;
use App\Kernel;

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine')->getManager();

try {
    // 1. Roles
    $roleAdmin = $em->getRepository(Role::class)->findOneBy(['code' => 'ADMIN']);
    if (!$roleAdmin) {
        $roleAdmin = (new Role())
            ->setCode('ADMIN')
            ->setLibelle('Administrateur');
        $em->persist($roleAdmin);
    }

    $roleMembre = $em->getRepository(Role::class)->findOneBy(['code' => 'MEMBRE']);
    if (!$roleMembre) {
        $roleMembre = (new Role())
            ->setCode('MEMBRE')
            ->setLibelle('Membre');
        $em->persist($roleMembre);
    }

    // 2. Membres fictifs
    $membresData = [
        ['Awa', 'Diop', 'awa.diop.demo@example.com', '770000001'],
        ['Moussa', 'Fall', 'moussa.fall.demo@example.com', '770000002'],
        ['Fatou', 'Ndiaye', 'fatou.ndiaye.demo@example.com', '770000003'],
    ];

    $membres = [];

    foreach ($membresData as $index => $data) {
        $membre = $em->getRepository(Membre::class)->findOneBy([
            'email' => $data[2],
        ]);

        if (!$membre) {
            $membre = (new Membre())
                ->setPrenom($data[0])
                ->setNom($data[1])
                ->setEmail($data[2])
                ->setTelephone($data[3]);

            $membre->valider();
            $em->persist($membre);
        }

        $membre->ajouterRole($roleMembre);

        if ($index === 0) {
            $membre->ajouterRole($roleAdmin);
        }

        $membres[] = $membre;
    }

    // 3. Type de campagne
    $type = $em->getRepository(TypeCampagne::class)->findOneBy([
        'libelle' => 'Tontine mensuelle',
    ]);

    if (!$type) {
        $type = (new TypeCampagne())
            ->setLibelle('Tontine mensuelle')
            ->setPeriodicite('mensuelle');

        $em->persist($type);
    }

    // 4. Campagne
    $campagne = $em->getRepository(Campagne::class)->findOneBy([
        'nom' => 'Tontine de démonstration',
    ]);

    if (!$campagne) {
        $debut = new DateTimeImmutable('today');

        $campagne = (new Campagne())
            ->setNom('Tontine de démonstration')
            ->setDescription('Campagne fictive pour tester la gestion des cotisations et des tours.')
            ->setMontantCotisation(10000)
            ->setNombreMembresMax(10)
            ->setDateDebut($debut)
            ->setDateFin($debut->modify('+6 months'))
            ->setTypeCampagne($type);

        $em->persist($campagne);
    }

    // Enregistrer les entités de base pour garantir leurs identifiants.
    $em->flush();

    // 5. Participations
    $participations = [];

    foreach ($membres as $index => $membre) {
        $participation = $em->getRepository(Participation::class)->findOneBy([
            'membre' => $membre,
            'campagne' => $campagne,
        ]);

        if (!$participation) {
            $participation = (new Participation())
                ->setMembre($membre)
                ->setCampagne($campagne)
                ->setOrdrePassage($index + 1);

            $em->persist($participation);
        }

        $participations[] = $participation;
    }

    $em->flush();

    // 6. Tours
    foreach ($participations as $index => $participation) {
        $numero = $index + 1;

        $tour = $em->getRepository(Tour::class)->findOneBy([
            'campagne' => $campagne,
            'numero' => $numero,
        ]);

        if (!$tour) {
            $tour = (new Tour())
                ->setNumero($numero)
                ->setDateTour(new DateTimeImmutable('+' . $index . ' month'))
                ->setMontantCagnotte(
                    $campagne->getMontantCotisation() * count($participations)
                )
                ->setEstCloture(false)
                ->setCampagne($campagne)
                ->setBeneficiaire($participation);

            $em->persist($tour);
        }
    }

    $em->flush();

    echo PHP_EOL . "=== INITIALISATION TERMINEE ===" . PHP_EOL;

    foreach ([
        Role::class => 'Rôles',
        Membre::class => 'Membres',
        TypeCampagne::class => 'Types de campagne',
        Campagne::class => 'Campagnes',
        Participation::class => 'Participations',
        Tour::class => 'Tours',
    ] as $class => $label) {
        echo $label . ' : ' . $em->getRepository($class)->count([]) . PHP_EOL;
    }

    echo PHP_EOL . "Campagne de démonstration : OK" . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, PHP_EOL . "ERREUR : " . $e->getMessage() . PHP_EOL);
    exit(1);
} finally {
    $em->getConnection()->close();
    $kernel->shutdown();
}