<?php

namespace App\Controller;

use App\Security\CategoryVoter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category')]
#[IsGranted('IS_AUTHENTICATED')] 
final class CategoryController extends AbstractController
{   
    #[Route('/delete/{id}', name: 'category_delete')]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')] 
    public function delete(int $id, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'DELETE FROM categories WHERE id = :id';

        $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id
        ]);

        return $this->redirectToRoute('dashboard_index');
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
                'userId' => $this->getUser()->getId(),
                'createdAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            ]);

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('category/new.html.twig', [
            'categories' => [],
        ]);
    }

    #[Route('/update/{id}', name: 'category_update')]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');

            $sql = "UPDATE categories SET name = :name, updated_at = :updatedAt WHERE id = :id";

            $entityManager->getConnection()->executeQuery($sql , [
                'name' => $name,
                'id' => $id,
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            ])->fetchAllAssociative();

            return $this->redirectToRoute('dashboard_index');
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
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function activity(int $id, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM categories WHERE id = :id';
        $category = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();

        $sql = 'SELECT activity_id, SUM(amount) AS count
            FROM records 
            WHERE DATE(created_at) = CURRENT_DATE 
            GROUP BY activity_id;
        ';
        $activityCount = $entityManager->getConnection()->executeQuery($sql)
            ->fetchAllKeyValue();

        $sql = 'SELECT a.* FROM activities AS a
                LEFT JOIN records AS r ON a.id = r.activity_id
                WHERE a.category_id = :categoryId
                GROUP BY a.id, a.name, a.unit
                ORDER BY MAX(r.created_at) DESC NULLS LAST';
        $activities = $entityManager->getConnection()->executeQuery($sql, [
            'categoryId' => $id,
        ])->fetchAllAssociative();

        foreach ($activities as $index => $activity) {
            $activities[$index]['count'] = $activityCount[$activity['id']] ?? 0;
        }
        
        return $this->render('category/view.html.twig', [
            'category' => $category,
            'activities' => $activities,
        ]);
    }
}
