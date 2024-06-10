<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categories;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'image (image file)',
                'mapped' => false,
                'required' => true,
                'constraints' => [new \Symfony\Component\Validator\Constraints\Image([
                    'maxSize' => '15M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'image/webp',
                    ],
                ])],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true, 
            ])
            ->add('couleur', TextType::class, [
                'label' => 'Couleur',
                'required' => true, 
            ])
            ->add('descriptionDetaille', TextareaType::class, [
                'label' => 'Description détaillée',
                'required' => true, 
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR', 
                'required' => true, 
            ])
            ->add('category', EntityType::class, [
                'label' => 'Choix de la catégorie',
                'class' => Categories::class,
                'choice_label' => 'titre', //  la propriété 'titre' de l'entité Category
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
