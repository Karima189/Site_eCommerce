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
    public function ajout_produit(Request $request, EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response
    {
        // $produit = new Produit($prix,'bleu');
        // $produit->setTaille('XL');
        // $produit2 = new Produit($prix+5,'rouge');
        // $produit2->setTaille('S');
        // $produit->descriptionDetaille='ABC';
        // dump($produit);
        // dd($produit2);
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
            $produit->setImage($newFilename);
        
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
