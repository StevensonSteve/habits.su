<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route('', name: 'category_index')]
    public function index(CategoryRepository $categoryRepository): Response 
    {
        $categories = $categoryRepository->findAll();

        // dd($categories);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/reading', name: 'todo_reading')]
    public function reading(): Response 
    {
        return $this->render('todo/reading.html.twig', [
            'controller_name' => 'TodoController',
        ]);
    }
}
