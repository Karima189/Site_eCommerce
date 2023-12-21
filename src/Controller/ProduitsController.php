<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitsController extends AbstractController
{
    #[Route('/produits/categorie/{categoryId}', name: 'produits_par_categorie')]
    public function produitsParCategorie(ProduitRepository $produitRepository, int $categoryId): Response
    {
        // Récupérer les produits avec category_id = 1
        $produits = $produitRepository->findBy(['category' => $categoryId]);

        // dd($produits);

        // Afficher la liste des produits dans le template Twig
        return $this->render('produits/produits.html.twig', [
            'produits' => $produits,
        ]);
    }
}
