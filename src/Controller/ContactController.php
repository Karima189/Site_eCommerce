<?php 
// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    
    #[Route('/contact', name: 'contact_show')]
    
    public function showChat(): Response
    {
        return $this->render('contact/show.html.twig');
    }
    #[Route('/contact/submit', name: 'contact_submit', methods: ['POST'])]

    public function submitQuestion(Request $request): Response
    {
        $question = $request->request->get('question');
        // Ajoutez ici la logique pour traiter la question (par exemple, l'enregistrer en base de données)
        
        // Redirigez l'utilisateur vers la page de discussion après la soumission du formulaire
        return $this->redirectToRoute('contact_show');
    }
}

