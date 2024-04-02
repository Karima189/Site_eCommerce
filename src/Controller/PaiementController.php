<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaiementController extends AbstractController
{
   
    // public function index(SessionInterface $session, UrlGeneratorInterface $urlGeneratorInterface , Request $request): Response
    // {
    //     \Stripe\Stripe::setApiKey('sk_test_51OICEgC3GA5BR02Af7eTScs2GgI29d4FpjzMiWRo625SCPzvudJNRQPg0A3ICZ9wTnCiXJadx9TrO7MRr9lVaXV800sjafT7mP');

    //     $recapitulatif = $session->get('recapitulatif', []);

    //     $lineItems = [];

    //     foreach ($recapitulatif as $recap) {
    //         if (isset($recap['prixTotalProduit']) && isset($recap['description'])) {
    //             $uniteAmout = round($recap['prixTotalProduit'] * 100);
    //             $lineItems[] = [
    //                 'price_data' => [
    //                     'currency' => 'eur',
    //                     'product_data' => [
    //                         'name' => $recap['description'],
    //                     ],
    //                     'unit_amount' => $uniteAmout,
    //                 ],
    //                 'quantity' => $recap['quantity'],
    //             ];
    //         }
    //     }

    //     // Frais de livraison
    //     $livraison = $session->get('livraison');
    //     $prixLivraison = $this->getPrixLivraison($livraison);


    //     // Ajouter les frais de livraison
    //     $lineItems[] = [
    //         'price_data' => [
    //             'currency' => 'eur',
    //             'product_data' => [
    //                 'name' => 'Frais de livraison',
    //             ],
    //             'unit_amount' => round($prixLivraison * 100),
    //         ],
    //         'quantity' => 1, // Vous pouvez ajuster la quantité selon votre besoin
    //     ];

    //     $checkout_session = \Stripe\Checkout\Session::create([
    //         'payment_method_types' => ['card'],
    //         'line_items' => $lineItems,
    //         'mode' => "payment",
    //         "success_url" => $urlGeneratorInterface->generate(
    //             'confirm_paiement_app',
    //             [],
    //                 $urlGeneratorInterface::ABSOLUTE_URL
    //         ),
    //         "cancel_url" => $urlGeneratorInterface->generate('cancel_payment', [], $urlGeneratorInterface::ABSOLUTE_URL),
    //     ]);

    //     return new RedirectResponse($checkout_session->url, 303);
    // }

    // // Fonction pour récupérer le prix de livraison en fonction de l'option sélectionnée
    // private function getPrixLivraison($livraison)
    // {
    //     switch ($livraison) {
    //         case 'point_relais':
    //             return 3.5; // Exemple : 3.50 EUR
    //         case 'express':
    //             return 6.90; // Exemple : 6.90 EUR
    //         case 'standard':
    //             return 4; // Exemple : 4.00 EUR
    //     }
    // }
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $session, UrlGeneratorInterface $urlGeneratorInterface , Request $request): Response
    {
        // \Stripe\Stripe::setApiKey('sk_test_51OICEgC3GA5BR02Af7eTScs2GgI29d4FpjzMiWRo625SCPzvudJNRQPg0A3ICZ9wTnCiXJadx9TrO7MRr9lVaXV800sjafT7mP');
    
        $recapitulatif = $session->get('recapitulatif', []);

        $prixLivraison = $recapitulatif['prixLivraison'] ?? null;
        $prixTotal = $recapitulatif['totalPrix'] ?? null ;
    
        $lineItems = [];
    
        foreach ($recapitulatif as $recap) {
            if (isset($recap['prixTotalProduit']) && isset($recap['description'])) {
                $uniteAmout = round($recap['prixTotalProduit'] * 100);
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $recap['description'],
                        ],
                        'unit_amount' => $uniteAmout,
                    ],
                    'quantity' => $recap['quantity'],
                ];
            }
        }
    
        // Récupérer la valeur de livraison sélectionnée par l'utilisateur depuis la requête
        $prixLivraison = $request->request->get('prixLivraison');

        // Stocker le prix de livraison dans la session Symfony
        $session->set('prixLivraison', $prixLivraison);
    
    
        // Stocker le prix de livraison dans la session Symfony
        $session->set('prixLivraison', $prixLivraison);
    
    
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
    public function confirmPaiement(): Response
    {
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
