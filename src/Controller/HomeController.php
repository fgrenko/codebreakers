<?php

namespace App\Controller;

use App\Service\DataAggregationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

class HomeController extends AbstractController
{
    #[Required]
    public DataAggregationService $dataAggregationService;

    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'aggregatedData' => $this->dataAggregationService->getAggregatedData()]);
    }
}
