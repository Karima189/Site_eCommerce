<?php
namespace App\Controller;

use App\Entity\Users;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasherPass): Response
    {

        $user = new Users();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        $plaintext = $user->getPassword();

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $hasherPass->hashPassword($user, $plaintext);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'user' => $form->createView(),
        ]);
    }
}

