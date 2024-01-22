<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list')]
    public function userList(UsersRepository $userRepository): Response
    {
        // Récupérer la liste des utilisateurs depuis le UserRepository
        $users = $userRepository->findAll();

        // Passer la liste des utilisateurs à la vue
        return $this->render('user/user_list.html.twig', [
            'users' => $users,
        ]);
    }
}

