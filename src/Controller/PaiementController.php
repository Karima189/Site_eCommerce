<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $session, UrlGeneratorInterface $urlGeneratorInterface): Response
    {

        \Stripe\Stripe::setApiKey('sk_test_51OICEgC3GA5BR02Af7eTScs2GgI29d4FpjzMiWRo625SCPzvudJNRQPg0A3ICZ9wTnCiXJadx9TrO7MRr9lVaXV800sjafT7mP');

        $recapitulatif = $session->get('recapitulatif', []);
        // dd($recapitulatif);

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
        return $this->redirectToRoute('app_home');
    }
}
