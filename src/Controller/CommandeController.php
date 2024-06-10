<?php

namespace App\Controller;

use App\Entity\Detail;

use App\Entity\Commande;
use App\Repository\DetailRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit; // Assurez-vous d'importer l'entité Produit

class CommandeController extends AbstractController
{

    #[Route('/commande/recap', name: 'order_add', methods: ['POST'])]
    public function summary(Request $request, SessionInterface $sessionInterface): Response
    {
        // Vérifier si l'utilisateur est connecté
        $commandeRequete = $request->getContent();
        $data = json_decode($commandeRequete, true); // décode le format envoyé par javascript (JSON ou string) et true ça veut dire les données json doivent etre tableu associatif
        $sessionInterface->set('recapitulatif', $data); // $data contient les informations des artciles séléctionnées en forme d'un tableau associatif 
        // Affichage récap
        if ($data) {
            if ($this->getUser()) {
                return new JsonResponse(['url' => '/confirmation-commande']);
            } else {
                return new JsonResponse(['url' => '/login']);
            }
        } else {
            return new JsonResponse(['url' => 'Veuillez séléctionner un produit']);
        }


    }

    #[Route('/confirmation-commande', name: 'confirmation_commande')]
    public function confirmationCommande(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        // Récupération des informations de la commande depuis les paramètres de requête
        $produits = $session->get('recapitulatif', []);
    

        if ($produits) {
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

            // Votre logique pour afficher la confirmation de la commande...

            return $this->render('commande/confirmation_commande.html.twig', ['infos' => $infos, 'totalPrix' => $totalProduits]);
        } else {
            return new Response("Veuillez séléctionner un produit");
        }

    }
    #[Route('/liste/commande', name: 'liste_commande')]
    public function list(CommandeRepository $commandeRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les commandes de l'utilisateur connecté
        $commandes = $commandeRepository->findBy(['user' => $user]);
        // dd($commandes);

        // Rendre la vue et passer les commandes à la vue
        return $this->render('commande/list.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commandes/{id}', name: 'details_commande')]
    public function detail(int $id, DetailRepository $detailRepository): Response
    {
        $details = $detailRepository->findBy(['commande' => $id]);


        return $this->render('commande/detail.html.twig', [
            'details' => $details,
            'idC'=>$id
        ]);
    }
    #[Route('/pdf/{id}', name: 'extrait_pdf')]
    public function extraitPDF(DompdfFactoryInterface $factory, $id, DetailRepository $detailRepository  ): Response
    {
        $details = $detailRepository->findBy(['commande' => $id]);



        $dompdf = $factory->create(); // création de pdf
        $html = '<h1 style="text-align:center;">Détails de la commande </h1>';
        foreach ($details as $detail) {
            // dump($detail);
        
            $html = $html . "<p>Description:".$detail->getProduit()->getDescription()."</p>";
             $html = $html . "<p>Prix:".$detail->getPrix()."</p>";
             $html = $html . "<p>Quantité de Produit:".$detail->getQuantité()."</p>";
             $html = $html . "<hr>";
        }
        $html = $html . "<p>Prix Total Commande:".$detail->getCommande()->getPrixTotal()."</p>";
        // dd($details);
        $dompdf->loadHtml($html);
        $dompdf->render();// pour afficher le résultat
        $response = new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="document.pdf"',
        ]);

        return $response;

    }
}



