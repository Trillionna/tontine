<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le nom est obligatoire.'),
                    new Assert\Length(max: 100),
                ],
                'attr' => ['placeholder' => 'Nom du membre'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le prénom est obligatoire.'),
                    new Assert\Length(max: 100),
                ],
                'attr' => ['placeholder' => 'Prénom du membre'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new Assert\NotBlank(message: 'L’adresse e-mail est obligatoire.'),
                    new Assert\Email(message: 'Saisissez une adresse e-mail valide.'),
                    new Assert\Length(max: 180),
                ],
                'attr' => ['placeholder' => 'exemple@domaine.com'],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le téléphone est obligatoire.'),
                    new Assert\Length(max: 30),
                ],
                'attr' => ['placeholder' => '+221 77 000 00 00'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
