<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route('', name: 'category_index')]
    public function index(EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM categories WHERE user_id = :userId';
        $categories = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => 1,
        ])->fetchAllAssociative();
        
        // dd($categories);
        
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    
    #[Route('/delete/{id}', name: 'category_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'DELETE FROM categories WHERE id = :id';

        $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id
        ])->fetchAllAssociative();

        return $this->redirectToRoute('category_index');
    }
}
