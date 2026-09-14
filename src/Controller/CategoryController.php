<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\RecordRepository;
use App\Security\CategoryVoter;
use App\Service\CategoryService;
use App\Service\StrikeService;
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
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly CategoryRepository $categoryRepository,
        private readonly RecordRepository $recordRepository,
        private readonly ActivityRepository $activityRepository,
    ) {}

    #[Route('/new', name: 'category_new')]
    public function new(Request $request): Response 
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');

            $this->categoryService->create($name);

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('category/new.html.twig', [
            'categories' => [],
        ]);
    }

    #[Route('/update/{id}', name: 'category_update')]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function update(int $id, Request $request): Response 
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');

            $this->categoryService->update($id, $name);

            return $this->redirectToRoute('dashboard_index');
        }

        $category = $this->categoryRepository->getCategoryById($id);

        return $this->render('category/update.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'category_view')]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function view(int $id, StrikeService $strikeService): Response 
    {
        $category = $this->categoryRepository->getCategoryById($id);
        $activityCount = $this->recordRepository->getRecordSumFromToday();
        $activities = $this->activityRepository->getLatestReportedActivitiesByCategoryId($category['id']);
        $strikes = $strikeService->getStrikes($category['id']);

        foreach ($activities as $index => $activity) {
            $popularRecords = $this->recordRepository->getPopularRecordsByActivityId($activity['id'], 4);
            
            $activities[$index]['strike'] = $strikes[$activity['id']] ?? 0;
            $activities[$index]['count'] = $activityCount[$activity['id']] ?? 0;
            $activities[$index]['popularRecords'] = $popularRecords;
        }
        
        return $this->render('category/view.html.twig', [
            'category' => $category,
            'activities' => $activities,
        ]);
    }

    #[Route('/delete/{id}', name: 'category_delete')]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')] 
    public function delete(int $id): Response 
    {
        $this->categoryService->delete($id);

        return $this->redirectToRoute('dashboard_index');
    }
}
