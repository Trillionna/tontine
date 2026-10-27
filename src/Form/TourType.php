<?php

namespace App\Form;

use App\Entity\Campagne;
use App\Entity\Participation;
use App\Entity\Tour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('dateTour', null, [
                'widget' => 'single_text'
            ])
            ->add('montantCagnotte')
            ->add('estCloture')
            ->add('campagne', EntityType::class, [
                'class' => Campagne::class,
                'choice_label' => 'id',
            ])
            ->add('beneficiaire', EntityType::class, [
                'class' => Participation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tour::class,
        ]);
    }
}
