<?php

namespace App\Controller;

use App\Entity\Detail;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit; // Assurez-vous d'importer l'entité Produit
use App\Repository\ProduitRepository;

class CommandeController extends AbstractController
{
    #[Route('/commande/recap', name: 'order_add', methods: 'POST')]
    public function summary(Request $request, EntityManagerInterface $em, SessionInterface $sessionInterface): Response
    {
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
        // dd($produits);
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

        $session->set('recapitulatif',$produits);
        // Vérifier s'il y a un total du prix dans le tableau $produits
        if (isset($produits[count($produits) - 1]['totalPrix'])) {
            $totalProduits = $produits[count($produits) - 1]['totalPrix'];
        }
        // dd($infos);
        // Votre logique pour afficher la confirmation de la commande...
        return $this->render('commande/confirmation_commande.html.twig', ['infos' => $infos, 'totalPrix' => $totalProduits]);
    }


    //  // Création de la commande avec les infos formulaire
    //  $commande = new Commande;
    //  $date = new \DateTime;
    //  $commande
    //      ->setUser($this->getUser()) // c'est user_id dans la table commande
    //      ->setCreatedAt($date)
    //      ->setState(0)
    //      ->setReference($date->format('YmdHis') . '-' . uniqid());

    //  $em->persist($commande);

    //  // Création des lignes de détails pour chacun des produits de la commande
    //  // Parcourir les articles sélectionnés
    //  foreach ($data as $articleData) {
    //      if (isset($articleData['id'])) {
    //          $produit = $em->getRepository(Produit::class)->find($articleData['id']);
    //          if ($produit) {
    //              $detail = new Detail();
    //              $detail->setCommande($commande);
    //              $detail->setProduit($produit);
    //              $detail->setQuantité($articleData['quantity']);
    //              $detail->setPrix($articleData['prixTotalProduit']);
    //              $articleData['taille'] == 'taille_unique' ? $detail->setTaille(null) : $detail->setTaille($articleData['taille']);
    //              $em->persist($detail);
    //          }
    //      }
    //  }
    //  // dd($data); 
    //  $em->flush();

    //  $infos = $em->getRepository(Detail::class)->findBy(['commande' => $commande]);
    //  // dd($infos[0]->getCommande()->getUser()->getEmail());
    //  // dd($infos[1]->getProduit()->getDescription());
}



