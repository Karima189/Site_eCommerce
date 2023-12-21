<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitsType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjoutProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_ajout_produit')]
    public function ajout_vetement(Request $request, EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();
            $newFilename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            // Déplacez le fichier vers le répertoire où vous souhaitez le stocker
            $uploadedFile->move(
                $this->getParameter('img_directory'),
                $newFilename
            );

            $couleur = $form->get('couleur')->getData();
            $taille = $form->get('taille')->getData();
            $description = $form->get('description')->getData();
            $descriptionDetaillee = $form->get('descriptionDetaille')->getData();
            $prix = $form->get('prix')->getData();
            $category = $form->get('category')->getData();

            // Enregistrement de nom de fichier dans la table de la BD
            $produit->setImage($newFilename);
            $produit->setTaille($taille);
            $produit->setCouleur($couleur);
            $produit->setDescription($description);
            $produit->setPrix($prix);
            $produit->setDescriptionDetaille($descriptionDetaillee);

            $categoryId = $form->get('category')->getData();
            $category = $categoriesRepository->find($categoryId);
            $produit->setCategory($category);
            
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ajout_produit/ajoutproduit.html.twig', [
            'controller_name' => 'ProduitController',
            'produit' => $form->createView(),
        ]);
    }
}
