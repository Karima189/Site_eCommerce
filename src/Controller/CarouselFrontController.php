<?php 
// src/Controller/CarouselFrontController.php

namespace App\Controller;

use App\Entity\CarouselFront;
use App\Form\CarouselFrontType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarouselFrontController extends AbstractController
{
    #[Route('/ajout_carousel', name: 'app_ajout_carousel')]
  
    public function ajoutCarousel(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carousel = new CarouselFront();
        $form = $this->createForm(CarouselFrontType::class, $carousel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();
            $newFilename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            // Déplacez le fichier vers le répertoire où vous souhaitez le stocker
            $uploadedFile->move(
                $this->getParameter('img_directory'), // Assurez-vous d'avoir un paramètre 'img_directory' configuré dans votre fichier services.yaml ou config/services.yaml
                $newFilename
            );

            // Enregistrement du nom de fichier dans la table de la BD
            $carousel->setImage($newFilename);

            $entityManager->persist($carousel);
            $entityManager->flush();

            return $this->redirectToRoute('app_home'); // Vous pouvez rediriger vers une autre route si nécessaire
        }

        return $this->render('carousel_front/ajout_carousel.html.twig', [
            'controller_name' => 'CarouselFrontController',
            'form' => $form->createView(),
        ]);
    }

    
}

