<?php

namespace App\Controller;

use App\Entity\AdresseCommande;
use App\Entity\Detail;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Repository\AdresseCommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $session, UrlGeneratorInterface $urlGeneratorInterface, Request $request, AdresseCommandeRepository $adresseCommandeRepository, ProduitRepository $produitRepository): Response
    {
        \Stripe\Stripe::setApiKey('sk_test_51OICEgC3GA5BR02Af7eTScs2GgI29d4FpjzMiWRo625SCPzvudJNRQPg0A3ICZ9wTnCiXJadx9TrO7MRr9lVaXV800sjafT7mP');

        $recapitulatif = $session->get('recapitulatif', []);
    

        // Récuperer ID de adresse et la chercher sur repository:

        $dernierElement = end($recapitulatif);

        $prixLivraison = isset($dernierElement['prixLivraison']) ? $dernierElement['prixLivraison'] : dd('erreur');
        $lineItems = [];
        foreach ($recapitulatif as $recap) {
            if(!isset($recap['description']) && isset($recap['id'])){
                $produit = $produitRepository->find($recap['id']);// retourne un produit avec toutes ses informations
                $description = $produit->getDescription();
            }
            if (isset($recap['prixTotalProduit'])) {
                $uniteAmout = round(($recap['prixUnitaire']) * 100);
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $recap['description'] ?? $description,
                        ],
                        'unit_amount' => $uniteAmout,
                    ],
                    'quantity' => $recap['quantity'],
                ];
            }
        }

        // Ajouter les frais de livraison
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Frais de livraison',
                ],
                'unit_amount' => round($prixLivraison * 100),
            ],
            'quantity' => 1, // Vous pouvez ajuster la quantité selon votre besoin
        ];

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => "payment",
            "success_url" => $urlGeneratorInterface->generate(
                'confirm_paiement_app',
                [],
                    $urlGeneratorInterface::ABSOLUTE_URL
            ),
            "cancel_url" => $urlGeneratorInterface->generate('cancel_payment', [], $urlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse($checkout_session->url, 303);
    }


    #[Route('/confirmation_paiement', name: "confirm_paiement_app")]
    public function confirmPaiement(EntityManagerInterface $em, SessionInterface $sessionInterface): Response
    {
        // Création de la commande avec les infos formulaire
        $commande = new Commande;
        $adresse = new AdresseCommande;
        $date = new \DateTime;
        $data = $sessionInterface->get('recapitulatif', []);
        

        $infosAdresse = $sessionInterface->get('adresse');
        $end = end($data);
        $commande
            ->setUser($this->getUser()) // c'est user_id dans la table commande
            ->setCreatedAt($date)
            ->setReference($date->format('YmdHis') . '-' . uniqid())
            ->setPrixTotal($end['totalPrix']);
        $adresse->setUser($this->getUser())
            ->setPrenom($infosAdresse->getPrenom())
            ->setNom($infosAdresse->getNom())
            ->setAdressePostale($infosAdresse->getAdressePostale())
            ->setCodePostal($infosAdresse->getCodePostal())
            ->setVille($infosAdresse->getVille())
            ->setPays($infosAdresse->getPays())
            ->setNumeroTelephone($infosAdresse->getNumeroTelephone())
            ->setCommande($commande);

        $em->persist($commande);
        $em->persist($adresse);

        // Création des lignes de détails pour chacun des produits de la commande
        // Parcourir les articles sélectionnés
        //  dd($data);
        foreach ($data as $articleData) {
            if (isset($articleData['id'])) {
                $produit = $em->getRepository(Produit::class)->find($articleData['id']);
                if ($produit) {
                    $detail = new Detail();
                    $detail->setCommande($commande); // ORM stocke automatiquement que l'id dans BDD
                    $detail->setProduit($produit);
                    $detail->setQuantité($articleData['quantity']);
                    $detail->setPrix($articleData['prixTotalProduit']);
                    $articleData['taille'] == 'taille_unique' ? $detail->setTaille(null) : $detail->setTaille($articleData['taille']);
                    $em->persist($detail);
                }
            }
        }
        // dd($data); 
        $em->flush();

        $infos = $em->getRepository(Detail::class)->findBy(['commande' => $commande]);

        $sessionInterface->remove('panier');
        $sessionInterface->remove('recapitulatif');
        $sessionInterface->remove('nbArticles');
        $sessionInterface->remove('adresse');

        // dd($infos[0]->getCommande()->getUser()->getEmail());
        return $this->render('paiement/confirmation_paiement.html.twig', []);
    }

    #[Route('/cancel-payment', name: 'cancel_payment')]
    public function cancelPayment(SessionInterface $sessionInterface): Response
    {
        $sessionInterface->remove('recapitulatif');
        $sessionInterface->remove('livraison'); // Supprimer l'option de livraison sélectionnée
        return $this->redirectToRoute('app_home');
    }
}
