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

class CommandeController extends AbstractController
{
    #[Route('/commande/recap', name: 'order_add', methods: 'POST')]
    public function summary(Request $request, EntityManagerInterface $em, SessionInterface $sessionInterface): Response
    {
        $commandeRequete = $request->getContent();

        $data = json_decode($commandeRequete, true);

        // Création de la commande avec les infos formulaire
        $commande = new Commande;
        $date = new \DateTime;
        $commande
            ->setUser($this->getUser())
            ->setCreatedAt($date)
            ->setState(0)
            ->setReference($date->format('YmdHis') . '-' . uniqid());

        $em->persist($commande);

        // Création des lignes de détails pour chacun des produits de la commande
        // Parcourir les articles sélectionnés
        foreach ($data as $articleData) {
            if (isset($articleData['id'])) {
                $produit = $em->getRepository(Produit::class)->find($articleData['id']);
                if ($produit) {
                    $detail = new Detail();
                    $detail->setCommande($commande);
                    $detail->setProduit($produit);
                    $detail->setQuantité($articleData['quantity']);
                    $detail->setPrix($articleData['prixTotalProduit']);
                    $detail->setTaille($articleData['taille']);

                    $em->persist($detail);

                    $em->flush();
                    $infos = $em->getRepository(Detail::class)->findBy(['produit' => $produit->getId()]);
                }
            }
        }

        $sessionInterface->set('recapitulatif', $infos);
        // Affichage récap

        return new JsonResponse(['url' => '/confirmation-commande']);
    }

    #[Route('/confirmation-commande', name: 'confirmation_commande')]
    public function confirmationCommande(Request $request, SessionInterface $session): Response
    {
        // Récupération des informations de la commande depuis les paramètres de requête
        $infos = $session->get('recapitulatif', []);
        // dd($infos);
        // Votre logique pour afficher la confirmation de la commande...
    
        return $this->render('commande/confirmation_commande.html.twig', ['infos' => $infos]);
    }
}



