<?php

namespace App\Controller;

use App\Entity\Detail;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit; // Assurez-vous d'importer l'entité Produit

class CommandeController extends AbstractController
{
    // #[Route('/commande/recap', name: 'order_add', methods: 'POST')]
    // public function summary(Request $request, EntityManagerInterface $em, SessionInterface $sessionInterface): Response
    // {
    //     $commandeRequete = $request->getContent();

    //     $data = json_decode($commandeRequete, true); // décode le format envoyé par javascript (JSON ou string); 
    //     $sessionInterface->set('recapitulatif', $data);
    //     // Affichage récap
    //     return new JsonResponse(['url' => '/confirmation-commande']);
    // }

    #[Route('/commande/recap', name: 'order_add', methods: ['POST'])]
    public function summary(Request $request, EntityManagerInterface $em, SessionInterface $sessionInterface): Response
    {
        // Vérifier si l'utilisateur est connecté
        $commandeRequete = $request->getContent();
        $data = json_decode($commandeRequete, true); // décode le format envoyé par javascript (JSON ou string); 
        $sessionInterface->set('recapitulatif', $data);
        // Affichage récap
        return new JsonResponse(['url' => '/confirmation-commande']);
    }

    #[Route('/confirmation-commande', name: 'confirmation_commande')]
    public function confirmationCommande(Request $request, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        // Récupération des informations de la commande depuis les paramètres de requête
        $produits = $session->get('recapitulatif', []);
        $infos = [];
        $totalProduits = 0;
        foreach ($produits as &$produit) {

            if (isset($produit['id'])) {
                $product = $produitRepository->findOneBy(['id' => $produit['id']]);
                if ($product) {
                    $description = $product->getDescription();
                    $produit['description'] = $description;
                    $infos[] = [
                        'data' => $produit,
                        'produits' => $product,
                    ];
                }
            }
        }
        $session->set('recapitulatif', $produits);
        // Vérifier s'il y a un total du prix dans le tableau $produits
        if (isset($produits[count($produits) - 1]['totalPrix'])) {
            $totalProduits = $produits[count($produits) - 1]['totalPrix'];
        }
        // dd($infos);
        // Votre logique pour afficher la confirmation de la commande...
        return $this->render('commande/confirmation_commande.html.twig', ['infos' => $infos, 'totalPrix' => $totalProduits]);
    }


}



