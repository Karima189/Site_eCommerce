<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $session): Response
    {

        \Stripe\Stripe::setApiKey('sk_test_51OtT8bFAMeAhvap1uTIzOpYX5cdl2UPqhXH2iB6DzM7JuQPOqUsjFXkAjg7H0htXG7Vo6jvlUkY57Spc8I1itsCn00zQ9vMflO');

        $recapitulatif = $session->get('recapitulatif', []);
        dd($recapitulatif);

        foreach($recapitulatif as $recap) {
            $uniteAmout = round($recap['prixTotalProduit']);
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $recap[''],
                    ]
                    ]
                    ];
        };

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            
        ]);


        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }
}
