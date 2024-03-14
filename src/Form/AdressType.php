<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\AdresseCommande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom ',
                'attr' => [
                    'placeholder' => 'Tapez votre nom'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom' ,
                'attr' => [
                    'placeholder' => 'Tapez votre prénom'
                ]
            ])
         
         
            ->add('adressePostale', TextType::class, [
                'label' => 'Adresse postale',
                'attr' => [
                    'placeholder' => 'ex: 8 rue de Lasoif'
                ]
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('pays', CountryType::class, [
                'label' => 'Pays'
            ])
            ->add('numeroTelephone', TelType::class, [
                'label' => 'Téléphone'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer adresse', 
                'attr' => [
                    'class' => 'btn btn-info btn-block mt-2'
                ]
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdresseCommande::class,
        ]);
    }
}

