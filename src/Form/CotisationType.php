<?php

namespace App\Form;

use App\Entity\Cotisation;
use App\Entity\Participation;
use App\Entity\Tour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('montantAttendu')
            ->add('datePaiement', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('modePaiement')
            ->add('participation', EntityType::class, [
                'class' => Participation::class,
                'choice_label' => static fn (Participation $p): string =>
                    'Participation #' . $p->getId(),
                'placeholder' => 'Choisir une participation',
            ])
            ->add('tour', EntityType::class, [
                'class' => Tour::class,
                'choice_label' => static fn (Tour $t): string =>
                    'Tour #' . $t->getNumero(),
                'placeholder' => 'Choisir un tour',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cotisation::class,
        ]);
    }
}