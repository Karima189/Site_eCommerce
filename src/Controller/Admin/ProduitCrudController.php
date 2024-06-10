<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('category')->autocomplete()->setRequired(true),
            ImageField::new('image')->setBasePath('/img')->setUploadDir('public/img/')->setRequired(false),
            IdField::new('id')->onlyOnIndex(),
            TextEditorField::new('description'),
            TextEditorField::new('descriptionDetaille'),
            NumberField::new('prix'),
            TextField::new('couleur')
        ];
    }
}
