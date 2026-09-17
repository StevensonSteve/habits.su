<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('')]
#[IsGranted('IS_AUTHENTICATED')] 
final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    #[Route('', name: 'dashboard_index', methods:['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        $categories = $this->dashboardService->getCategories($user);

        return $this->render('dashboard/index.html.twig', [
            'categories' => $categories,
            'user' => $user,
        ]);
    }
}
