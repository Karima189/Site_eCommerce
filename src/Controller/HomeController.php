<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProduitRepository $produitRepository): Response
    {
        // Identifiants spécifiques des 4 produits que vous souhaitez afficher
        $productIdsToDisplay = [22, 27, 29, 31];

        // Récupérer les produits par leurs identifiants et la catégorie = 2
        $categoryId = 2;
        $produits = $produitRepository->findBy(['id' => $productIdsToDisplay, 'category' => $categoryId]);
        // Identifiants spécifiques des produits de la catégorie=1 que vous souhaitez afficher dans le carousel
        $vetementIds = [63,53,57];
        $categoryIdVetements=1;

        // Récupérer les produits de la catégorie=1 par leurs identifiants
        $vetements = $produitRepository->findBy(['id' => $vetementIds, 'category' =>  $categoryIdVetements]);
        // Afficher la liste des produits dans le template Twig
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'produits' => $produits,
            'vetements' => $vetements,
        ]);
    }

 


}
