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
    public function summary(Request $request,SessionInterface $sessionInterface): Response
    {
        // Vérifier si l'utilisateur est connecté
        $commandeRequete = $request->getContent();
        $data = json_decode($commandeRequete, true); // décode le format envoyé par javascript (JSON ou string) et true ça veut dire les données json doivent etre tableu associatif
        $sessionInterface->set('recapitulatif', $data); // $data contient les informations des artciles séléctionnées en forme d'un tableau associatif 
        // Affichage récap
        if($data){
            if($this->getUser()){
                return new JsonResponse(['url' => '/confirmation-commande']);
            }else{
                return new JsonResponse(['url' => '/login']);
            }
        }else{
            return new JsonResponse(['url' => 'Veuillez séléctionner un produit']);
        }
       
     
    }
    
    #[Route('/confirmation-commande', name: 'confirmation_commande')]
    public function confirmationCommande( SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        // Récupération des informations de la commande depuis les paramètres de requête
        $produits = $session->get('recapitulatif', []);
        if($produits){
            $infos = [];
            $totalProduits = 0;
            foreach ($produits as &$produit) {  
                if (isset($produit['id'])) {
                    $product = $produitRepository->findOneBy(['id' => $produit['id']]);
                    if ($product) { 
                        $description = $product->getDescription();
                        $produit['description'] = $description;
                        $infos[] = [
                            'data' => $produit,// un tableau associatif 
                            'produits' => $product, // un tableau d'objets recupéré de la BDD
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
        }else{
            return new Response("Veuillez séléctionner un produit");
        }

    }


}



