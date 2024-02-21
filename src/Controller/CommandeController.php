<?php

namespace App\Controller;

use App\Entity\Detail;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit; // Assurez-vous d'importer l'entité Produit

class CommandeController extends AbstractController
{
    #[Route('/commande/recap', name: 'order_add', methods: 'POST')]
    public function summary(Request $request, EntityManagerInterface $em): Response
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

        // Affichage récap

        return $this->redirectToRoute('recapp', ['infos' => json_encode($infos)]);
    }

    #[Route('/confirmation-commande', name: 'confirmation_commande')]
    public function confirmationCommande(Request $request): Response
    {
        // Récupération des informations de la commande depuis les paramètres de requête
        $infos = json_decode($request->query->get('infos'), true);
    
        // Votre logique pour afficher la confirmation de la commande...
    
        return $this->render('confirmation_commande.html.twig', ['infos' => $infos]);
    }
}



