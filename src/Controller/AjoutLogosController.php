<?php

namespace App\Controller;

use App\Entity\Logos;
use App\Form\LogosType;
use App\Repository\LogosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjoutLogosController extends AbstractController
{
    #[Route('/ajout_logo', name: 'app_ajout_logo')]
    public function ajoutLogo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $logo = new Logos();
        $form = $this->createForm(LogosType::class, $logo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image_logo')->getData();
            $newFilename = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            // Déplacez le fichier vers le répertoire où vous souhaitez le stocker
            $uploadedFile->move(
                $this->getParameter('img_directory'), // Assurez-vous d'avoir un paramètre 'img_directory' configuré dans votre fichier services.yaml ou config/services.yaml
                $newFilename
            );

            $descriptionLogo = $form->get('description_logo')->getData();

            // Enregistrement du nom de fichier et de la description dans la table de la BD
            $logo->setImageLogo($newFilename);
            $logo->setDescriptionLogo($descriptionLogo);

            $entityManager->persist($logo);
            $entityManager->flush();

            return $this->redirectToRoute('app_home'); // Vous pouvez rediriger vers une autre route si nécessaire
        }

        return $this->render('ajout_logos/ajout_logo.html.twig', [
            'controller_name' => 'AjoutLogosController',
            'logo' => $form->createView(),
        ]);
    }
   
}

