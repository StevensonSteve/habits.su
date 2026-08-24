<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('')]
#[IsGranted('IS_AUTHENTICATED_FULLY')] 
final class DashboardController extends AbstractController
{
    #[Route('', name: 'dashboard_index')]
    public function index(EntityManagerInterface $entityManager): Response 
    {
        $user = $this->getUser();

        $sql = 'SELECT * FROM categories WHERE user_id = :userId ORDER BY updated_at DESC';
        $categories = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
        ])->fetchAllAssociative();
        
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
