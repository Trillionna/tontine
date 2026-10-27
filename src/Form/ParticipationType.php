<?php

namespace App\Form;

use App\Entity\Campagne;
use App\Entity\Membre;
use App\Entity\Participation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateAdhesion', null, [
                'widget' => 'single_text'
            ])
            ->add('ordrePassage')
            ->add('aBeneficie')
            ->add('membre', EntityType::class, [
                'class' => Membre::class,
                'choice_label' => 'id',
            ])
            ->add('campagne', EntityType::class, [
                'class' => Campagne::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
