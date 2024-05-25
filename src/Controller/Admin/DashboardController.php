<?php

namespace App\Controller\Admin;

use App\Entity\CarouselFront;
use App\Entity\TailleProduit;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Users');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Carousel', 'fas fa-list', CarouselFront::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categories::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Produit::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
    }
}
