<?php

namespace App\Controller;

use App\Form\AdressType;
use App\Entity\AdresseCommande;
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
    public function list(EntityManagerInterface $em, SessionInterface $sessionInterface): Response
    {
        // dd($sessionInterface->get('recapitulatif',[]));  
        $user = $this->getUser();
        $addresses = $em->getRepository(AdresseCommande::class)->findBy(['user' => $user]);

        return $this->render('adress/list.html.twig', [
            'addresses' => $addresses,
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

