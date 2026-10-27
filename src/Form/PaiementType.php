<?php

namespace App\Form;

use App\Entity\Paiement;
use App\Entity\Participation;
use App\Entity\Tour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('datePaiement', null, [
                'widget' => 'single_text'
            ])
            ->add('modePaiement')
            ->add('statut')
            ->add('participation', EntityType::class, [
                'class' => Participation::class,
                'choice_label' => 'id',
            ])
            ->add('tour', EntityType::class, [
                'class' => Tour::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
