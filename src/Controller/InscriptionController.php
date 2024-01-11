<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\InscriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_user')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasherPass): Response
    {
        $user = new Users();
        $form = $this->createForm( InscriptionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $hasherPass->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $em->persist ($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/user.html.twig', [
            'controller_name' => 'Inscrivez-vous !',
            'user' => $form->createView()
        ]);
    }
}
