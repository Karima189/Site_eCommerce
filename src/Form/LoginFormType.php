<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'Email', // Vous pouvez personnaliser l'étiquette selon vos besoins
                'attr' => ['placeholder' => 'Votre email'],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Mot de passe', // Vous pouvez personnaliser l'étiquette selon vos besoins
                'attr' => ['placeholder' => 'Votre mot de passe'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configurez vos options si nécessaire
        ]);
    }
}