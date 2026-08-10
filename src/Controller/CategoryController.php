<?php

namespace App\Controller;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    private const TEST_USER_ID = 1;

    #[Route('', name: 'category_index')]
    public function index(EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM categories WHERE user_id = :userId';
        $categories = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => self::TEST_USER_ID,
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

    #[Route('/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');

            $sql = "INSERT INTO categories (name, user_id, created_at, updated_at) 
                VALUES (:name, :userId, :createdAt, :updatedAt)";

            $entityManager->getConnection()->executeQuery($sql , [
                'name' => $name,
                'userId' => self::TEST_USER_ID,
                'createdAt' => new DateTimeImmutable()->format("Y-m-d H:m:s"),
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:m:s"),
            ])->fetchAllAssociative();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'categories' => [],
        ]);
    }

    #[Route('/update/{id}', name: 'category_update')]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');

            $sql = "UPDATE categories SET name = :name, updated_at = :updatedAt WHERE id = :id";

            $entityManager->getConnection()->executeQuery($sql , [
                'name' => $name,
                'id' => $id,
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:m:s"),
            ])->fetchAllAssociative();

            return $this->redirectToRoute('category_index');
        }

        $sql = 'SELECT * FROM categories WHERE id = :id';
        $category = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();

        return $this->render('category/update.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'category_view')]
    public function activity(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM categories WHERE id = :id';
        $category = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();

        $sql = 'SELECT * FROM activities WHERE category_id = :categoryId';
        $activities = $entityManager->getConnection()->executeQuery($sql, [
            'categoryId' => $id,
        ])->fetchAllAssociative();
        
        return $this->render('category/view.html.twig', [
            'category' => $category,
            'activities' => $activities,
        ]);
    }
}
