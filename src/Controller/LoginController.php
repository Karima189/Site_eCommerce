<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
#[Route('/loginold', name: 'app_loginold')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
// Récupérer l'erreur de la session, s'il y en a une
$error = $authenticationUtils->getLastAuthenticationError();

// Dernier nom d'utilisateur saisi par l'utilisateur
$lastUsername = $authenticationUtils->getLastUsername();

 // Si l'utilisateur est connecté, redirigez-le vers l'index du dossier home
 if ($this->getUser()) {
    return $this->redirectToRoute('app_home');
}



return $this->render('login/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
}

#[Route('/logout', name: 'app_logout')]
public function logout()
{
// Cette méthode peut rester vide, elle sera interceptée par le composant de sécurité lors de la déconnexion
}
}