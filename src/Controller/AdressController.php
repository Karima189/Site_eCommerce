<?php

namespace App\Controller;

use App\Form\AdressType;
use App\Entity\AdresseCommande;
use App\Repository\AdresseCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdressController extends AbstractController
{
    #[Route('compte/adresses/ajouter', name: 'account_address')]
    public function add(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $adress = new AdresseCommande();
        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $session->set('adresse', $data);
            $session->set('order', 1);


            if ($session->get('order') === 1) {       //Redirection vers la commande si celle-ci a été amorcée
                $session->set('order', 0);
                return $this->redirectToRoute('app_paiement');
            }
            return $this->redirectToRoute('account_address');
        }

        return $this->render('adress/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/compte/adresses', name: 'account_addresses')]
    public function list(EntityManagerInterface $em, SessionInterface $sessionInterface, Request $request, AdresseCommandeRepository $adresseCommandeRepository): Response
    {
        // dd($sessionInterface->get('recapitulatif',[])); 
        $user = $this->getUser();
        $addresses = $em->getRepository(AdresseCommande::class)->findBy(['user' => $user]);
    
    
        // cette partie c'est si on a 2 adresses identiques on veut afficher cette adresse une seule fois 
        // On converti le tableau d'objets $addresses en tableau associatif pour pouvoir comparer grâce à la methode array_unique();
        $addressData = array_map(function ($address) {
            return $address->getAdressePostale();
        }, $addresses);
      
        // On supprime les doublons grâce à array_unique();
        $uniqueAddressesData = array_unique($addressData);
        
        // On reconverti les adresses de tableaux associatifs en tableau d'objets :
        $uniqueAddresses = [];

        foreach ($uniqueAddressesData as $address) {
            $uniqueAddress = $em->getRepository(AdresseCommande::class)->findOneBy(['adressePostale' => $address]);
            // Assurez-vous que $uniqueAddress n'est pas NULL avant de l'ajouter au tableau
            if ($uniqueAddress !== null) {
                $uniqueAddresses[] = $uniqueAddress;
            }
        }
       

        $url = $request->query->all();
        if (isset($url['id'])) {
            $adresse = $adresseCommandeRepository->findOneBy(['id' => $url['id']]);
            $sessionInterface->set('adresse', $adresse);
            return $this->redirectToRoute('app_paiement');
        }
        // if(isset(00))

        return $this->render('adress/list.html.twig', [
            'addresses' => $uniqueAddresses,
        ]);
    }

    #[Route('verification/adresse', name: 'adresse_verify')]
    public function verification(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $addresses = $em->getRepository(AdresseCommande::class)->findBy(['user' => $user]);

        if ($addresses) {
            return $this->redirectToRoute('account_addresses');
        } else {
            return $this->redirectToRoute('account_address');
        }
    }

    #[Route('/compte/adresses/supprimer/{id}', name: 'delete_address')]
    public function delete(AdresseCommande $address, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($address);
        $entityManager->flush();

        $this->addFlash('success', 'Adresse supprimée avec succès.');

        return $this->redirectToRoute('account_addresses');
    }
}

