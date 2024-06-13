<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Logos;
use App\Entity\Produit;
use App\Entity\Categories;
use App\Entity\CarouselFront;
use App\Entity\TailleProduit;
use App\Repository\CommandeRepository;
use App\Repository\DetailRepository;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private DetailRepository $detailRepository,private CommandeRepository $commandeRepository
    ) {}
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //


        // Chart 1
        $statistique1=$this->commandeRepository->StatistiqueNombreCommande();
        $stat1=[ ];
        $statDate=[];// elle contient les dates 
        foreach ($statistique1 as $itemStat) {
            $tabStat1=[$itemStat['Date'],$itemStat['TotalCommandes'] ];
            array_push($stat1,$tabStat1);
            array_push($statDate,$itemStat['Date']);
            
        }
        $test= [10,20,40,50,60,30,80];
        $series = [
            [
                "name" => "nombre de commandes Passés",
                "data" => $stat1
            ], 
        ];
    
        $chart = new Highchart();
        $chart->chart->renderTo('container');  // The #id of the div where to render the chart
        $chart->title->text('Statistique de nombre de commandes passées selon la date');
        $chart->xAxis->title(['text' => "Horizontal axis title"]);
        $chart->yAxis->title(['text' => "Vertical axis title"]);
        $chart->xAxis->categories($statDate);

        $chart->series($series);
       
        // charte 2 
        $statistique2=$this->detailRepository->StatistiqueDetailProduit();
        $stat2=[ ];
        foreach ($statistique2 as $itemStat) {
            $tabStat=[$itemStat['description'],$itemStat['venteCount'] ];
            array_push($stat2,$tabStat);
            
        }
        // dump($stat1);
        // dd($statistique);
        $chart2 = new Highchart();
        $chart2->chart->renderTo('container2');
        $chart2->chart->type('pie');
        $chart2->title->text('Statistique des produits vendues');
        $chart2->plotOptions->series(
            [
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '{point.name}: {point.y:.1f}%'
                ]
            ]
        );
        $chart2->plotOptions->pie([
            'colors' => ['#FF5733', '#3498DB', '#F1C40F', '#27AE60', '#9B59B6'],
            // Add other options as needed
        ]);
        
        // $chart2->tooltip->headerFormat('<span style="font-size:11px">{series.name}</span><br>');
        // $chart2->tooltip->pointFormat('<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>');

   
        $chart2->series(
            [
                [
                    'name' => 'Browser share',
                    'colorByPoint' => true,
                    'data' => $stat2
                ]
            ]
        );

      
        return $this->render('admin/my-dashboard.html.twig', ['chart' => $chart, 'chart2'=>$chart2]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dashboard Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Logos', 'fa-solid fa-image', Logos::class);
        yield MenuItem::linkToCrud('Carousel', 'fa-solid fa-image', CarouselFront::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categories::class);
        yield MenuItem::linkToCrud('Produits', ' fa-solid fa-cart-shopping', Produit::class);
        yield MenuItem::linkToCrud('Users', 'fa-solid fa-user', User::class);
    }
}
