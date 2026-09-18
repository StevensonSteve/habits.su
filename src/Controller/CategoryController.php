<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Security\CategoryVoter;
use App\Service\ActivityService;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category')]
#[IsGranted('IS_AUTHENTICATED')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly CategoryRepository $categoryRepository,
        private readonly ActivityService $activityService,
    ) {}

    #[Route('/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $name = $request->request->get('name');

            $this->categoryService->create($name);

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('category/new.html.twig', [
            'categories' => [],
        ]);
    }

    #[Route('/update/{id}', name: 'category_update', methods: ['GET', 'POST'])]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function update(int $id, Request $request): Response
    {
        $category = $this->categoryRepository->getCategoryById($id);

        if ($request->getMethod() === 'POST') {
            $name = $request->request->get('name');

            $this->categoryService->update($id, $name);

            return $this->redirectToRoute('category_view', [
                'id' => $category['id'],
            ]);
        }

        return $this->render('category/update.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'category_view', methods: ['GET'])]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function view(int $id): Response
    {
        $user = $this->getUser();
        $category = $this->categoryRepository->getCategoryById($id);
        $activities = $this->activityService->getActivitiesWithStats($category['id'], $user);

        return $this->render('category/view.html.twig', [
            'category' => $category,
            'activities' => $activities,
        ]);
    }

    #[Route('/delete/{id}', name: 'category_delete', methods: ['GET'])]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function delete(int $id): Response
    {
        $this->categoryService->delete($id);

        return $this->redirectToRoute('dashboard_index');
    }
}
