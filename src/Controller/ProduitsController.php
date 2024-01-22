<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    #[Route('/produit/{id}', name: 'afficher_produit')]
    public function afficherProduit(Produit $produit): Response
    {
        return $this->render('produits/detailsProduit.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/ajouter-au-panier/{id}', name: 'ajouter_au_panier')]
    public function ajouterAuPanier(Produit $produit, SessionInterface $session): Response
    {
        // Récupérer le panier actuel depuis la session
        $panier = $session->get('panier', []);

        // Ajouter le produit au panier
        $panier[] = [
            'id' => $produit->getId(),
            'image' => $produit->getImage(),
            'description' => $produit->getDescription(),
            'prix' => $produit->getPrix(),
        ];

        // Mettre à jour le panier dans la session
        $session->set('panier', $panier);

        // Rediriger vers la page précédente ou une autre page
        return $this->redirectToRoute('produits_par_categorie', ['categoryId' => $produit->getCategory()->getId()]);
    }
    
}
