<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;



class PanierController extends AbstractController
{
    //pour ajouter des produits au panier
    #[Route('/panier', name: 'afficher_panier')]
    public function afficherPanier(SessionInterface $session , Request $request): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);
        // // Récupérer la valeur du cookie 'panier'
        // $panier = json_decode($request->cookies->get('panier'), true) ?? [];
        return $this->render('panier/afficher_panier.html.twig', [
            'panier' => $panier,
        ]);
    }
    
    // pour supprimer un produit du panier
    #[Route('/supprimer-du-panier/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier(int $id, SessionInterface $session , Request $request): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);
        // Récupérer la valeur du cookie 'panier'
        // $panier = json_decode($request->cookies->get('panier'), true) ?? [];
        
       
        // Rechercher l'index du produit dans le panier
        foreach ($panier as $index => $produit) {
            if ($produit['id'] === $id) {
                // Supprimer le produit du panier
                unset($panier[$index]);
                // Réindexer le tableau après la suppression
                $panier = array_values($panier);
                // Mettre à jour le panier dans la session
                $session->set('panier', $panier);
                $nbArticles = count($panier);
                $session->set('nbArticles', $nbArticles);
                // Rediriger vers la page du panier
                return $this->redirectToRoute('afficher_panier');
                // break;
            }
        }
       // Mettre à jour le cookie 'panier'
    //    $response = new Response();
    //    $response->headers->setCookie(new Cookie('panier', json_encode($panier)));
    //    $response->headers->setCookie(new Cookie('nbArticles', count($panier)));
    //    $response->send();
        // Si le produit n'est pas trouvé, rediriger vers la page du panier
        return $this->redirectToRoute('afficher_panier');
        
    }
   
  
    // pour vider le panier
    #[Route('/vider-panier', name: 'vider_panier')]
    public function viderPanier(SessionInterface $session): Response
    {
        // Vider le panier en supprimant la clé 'panier', 'nbArticles' et 'recapitulatif' de la session
        $session->remove('panier');
        $session->remove('nbArticles');
        $session->remove('recapitulatif');
        // $response = new Response();
        // $response->headers->clearCookie('panier');
        // $response->headers->clearCookie('nbArticles');
        // $response->send();

        
        return $this->redirectToRoute('afficher_panier');
    }





}
