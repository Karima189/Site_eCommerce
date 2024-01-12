<?php

namespace App\Form;

use App\Entity\Logos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_logo', FileType::class, [
                'label' => 'Image du logo',
                'mapped' => false, // N'est pas lié directement à l'entité
                'required' => true,
            ])
            ->add('description_logo', TextType::class, [
                'label' => 'Description du logo',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Logos::class,
        ]);
    }
}

