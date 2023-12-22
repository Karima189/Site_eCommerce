<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierController extends AbstractController
{
    //pour ajouter des produits au panier
    #[Route('/panier', name: 'afficher_panier')]
    public function afficherPanier(SessionInterface $session): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        return $this->render('panier/affichage_panier.html.twig', [
            'panier' => $panier,
        ]);
    }
    // pour supprimer un produit du panier
    #[Route('/supprimer-du-panier/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier(int $id, SessionInterface $session): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        // Rechercher l'index du produit dans le panier
        $index = array_search(['id' => $id], array_column($panier, 'id'));

        // Si le produit est trouvé, le supprimer du panier
        if ($index !== false) {
            unset($panier[$index]);
            // Réindexer le tableau après la suppression
            $panier = array_values($panier);
            // Mettre à jour le panier dans la session
            $session->set('panier', $panier);
        }

        // Rediriger vers la page du panier
        return $this->redirectToRoute('afficher_panier');
    }
}
