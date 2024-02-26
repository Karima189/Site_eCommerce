<?php

namespace App\Controller;

use App\Entity\Produit;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use App\Repository\ProduitRepository;
use App\Repository\TailleProduitRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitsController extends AbstractController
{
    #[Route('/produits/categorie/{categoryId}', name: 'produits_par_categorie')]
    public function produitsParCategorie(ProduitRepository $produitRepository, Request $request, int $categoryId): Response
    {
        // Récupérer les produits avec category_id = 1
        $produitsQuery = $produitRepository->findBy(['category' => $categoryId]);

        $adapter = new ArrayAdapter($produitsQuery);
        $produits = new Pagerfanta($adapter);

        // Définir le nombre d'éléments par page
        $produits->setMaxPerPage(15);

        // Récupérer la page demandée depuis la requête
        $currentPage = $request->query->get('page', 1);

        $produits->setCurrentPage($currentPage);

        // dd($produits);
        switch ($categoryId) {
            case '1':
                $phrase = 'Chez Nous vous trouvez un large choix de vetements pour un look exellent et attirant!';
                break;
            case '2':
                $phrase = 'Vous trouverez un large choix de Make Up pour une beauté impréssionnante !
                ';
                break;
            case '3':
                $phrase = 'Une large gamme de bijoux pour une beauté incontournable';
                break;
            case '4':
                $phrase = ' Des Montres Raffinées et Luxes spécialements pour Vous !';
                break;
                default:
                $phrase = null;
            }
            
            // dd($produits);

        // Afficher la liste des produits dans le template Twig
        return $this->render('produits/produits.html.twig', [
            'produits' => $produits,
            'phrase' => $phrase,
            'categoryId' => $categoryId
        ]);
    }

    #[Route('/produit/{id}', name: 'afficher_produit')]
    public function afficherProduit(Produit $produit, TailleProduitRepository $tailleProduit): Response
    {
        $categorie = $produit->getCategory();
        // dd($categorie);
        $nomProduit = strtolower($produit->getDescription());

        // On déclare les variables null pour éviter les erreurs dans les endroits où on en a pas besoin (exemple, maquillage);
        $nomTaille = null;
        $tabTaille = null;

        // Si le produit contient l'un des mots clés dans sa description, alors il rentre dans cette condition et rempli LE tableau tabTaille en fonction du produit et ajoute le style de taille (cm,mm) également en fonction du nom.
        if (str_contains($nomProduit, 'bague')) {
            $tabTaille = ['44', '46', '48', '50', '52', '54', '56', '58', '60', '62', '64'];
            $nomTaille = "mm";
        } else if (str_contains($nomProduit, 'collier')) {
            $tabTaille = ['35', '40', '45', '50', '55', '60'];
            $nomTaille = 'cm';
        } else if (str_contains($nomProduit, 'bracelet') || $categorie->getId() == 4) {
            $tabTaille = ['14', '15', '16', '17', '18'];
            $nomTaille = 'cm';
        } else if ($categorie->getId() == 1) {
            $tabTaille = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        }


        return $this->render('produits/detailsProduit.html.twig', [
            'produit' => $produit,
            'nomTaille' => $nomTaille,
            'tabTaille' => $tabTaille
        ]);
    }

    #[Route('/ajouter-au-panier/{id}', name: 'ajouter_au_panier')]
    public function ajouterAuPanier(Produit $produit, SessionInterface $session, Request $request): JsonResponse
    {
        // Récupérer le panier actuel depuis la session
        $panier = $session->get('panier', []);

        $params = $request->query->all();

        $tailles = $params['taille'] ?? [];

        // Ajouter le produit au panier
        if (is_array($tailles)) {
            foreach ($tailles as $taille) {
                if ($taille !== "") {
                    $panier[] = [
                        'id' => $produit->getId(),
                        'image' => $produit->getImage(),
                        'description' => $produit->getDescription(),
                        'prix' => $produit->getPrix(),
                        'taille' => $taille
                    ];
                } else {
                    $panier[] = [
                        'id' => $produit->getId(),
                        'image' => $produit->getImage(),
                        'description' => $produit->getDescription(),
                        'prix' => $produit->getPrix(),
                        'taille' => "taille_unique"
                    ];
                }
            }
        }

        

        // Mettre à jour le panier dans la session
        $session->set('panier', $panier);

        $nbArticles = count($session->get('panier'));

        $session->set('nbArticles', $nbArticles);

        // Rediriger vers la page précédente ou une autre page
        $response = new JsonResponse(['nbArticles' => $nbArticles]);

        return $response;
    }

    // Code de Mitra:

    // #[Route('/ajout-panier/{id}', name: 'ajout_panier')]
    // public function ajoutPanier($id, ProduitRepository $pr, SessionInterface $session, Request $rq): Response
    // {
    //     $quantite = $rq->query->get("qte", 1) ?: 1;
    //     $produit = $pr->find($id);
    //     $panier = $session->get("panier", []); // on récupère ce qu'il y a dans le panier en session

    //     $produitDejaDansPanier = false;
    //     foreach ($panier as $indice => $ligne) {
    //         if ($produit->getId() == $ligne["produit"]->getId()) {
    //             $panier[$indice]["quantite"] += $quantite;
    //             $produitDejaDansPanier = true;
    //             break;  // pour sortir de la boucle foreach
    //         }
    //     }
    //     if (!$produitDejaDansPanier) {
    //         $panier[] = ["quantite" => $quantite, "produit" => $produit];  // on ajoute une ligne au panier => $panier est un array d'array
    //     }


    //     $session->set("panier", $panier);

    //     $nb = 0;
    //     foreach ($panier as $ligne) {
    //         $nb += $ligne["quantite"];
    //     }
    //     return $this->json($nb);
    // }

}
