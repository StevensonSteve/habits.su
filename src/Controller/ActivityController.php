<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\AtivityUpsertDto;
use App\Dto\DatesFilterDto;
use App\Enum\ActivityUnit;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\RecordRepository;
use App\Security\ActivityVoter;
use App\Security\CategoryVoter;
use App\Service\ActivityService;
use App\Service\GoalService;
use App\Service\RecordService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/activity')]
#[IsGranted('IS_AUTHENTICATED')]
final class ActivityController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityService $activityService,
        private readonly GoalService $goalService,
        private readonly RecordRepository $recordRepository,
        private readonly RecordService $recordService,
    ) {}

    #[Route('/{id}', name: 'activity_view')]
    #[IsGranted(ActivityVoter::MANAGE, subject: 'id')]
    public function view(int $id, Request $request): Response
    {
        $filter = $request->request->get('filter', RecordService::FILTER_PERIOD_TODAY);
        if ($filter != 'own') {
            $datesFilter = $this->recordService->getDateRange($filter);
        } else {
            $datesFilter = new DatesFilterDto(
                dateFrom: new DateTimeImmutable($request->request->get('dateFrom', RecordService::FILTER_PERIOD_TODAY)),
                dateTo: new DateTimeImmutable($request->request->get('dateTo', RecordService::FILTER_PERIOD_TODAY)),
            );
        }

        $activity = $this->activityRepository->getActivityById($id);
        $records = $this->recordRepository->getRecordsByActivityId($activity['id'], $datesFilter);
        $activityCount = $this->recordRepository->getActivityCountFromRecords($id, $datesFilter);
        $activitySum = $this->recordRepository->getActivitySumFromFecords($id, $datesFilter);
        $category = $this->categoryRepository->getCategoryById($activity['category_id']);

        return $this->render('activity/view.html.twig', [
            'buttons' => RecordService::RECORD_FILTERS,
            'activity' => $activity,
            'category' => $category,
            'activityCount' => $activityCount,
            'activitySum' => $activitySum,
            'records' => $records,
            'filter' => $filter,
            'dateTo' => $datesFilter->dateTo->format('Y-m-d'),
            'dateFrom' => $datesFilter->dateFrom->format('Y-m-d'),
        ]);
    }

    #[Route('/delete/{id}', name: 'activity_delete')]
    #[IsGranted(ActivityVoter::MANAGE, subject: 'id')]
    public function delete(int $id): Response
    {
        $activity = $this->activityRepository->getActivityById($id);

        $this->activityService->delete($id);

        return $this->redirectToRoute('category_view', [
            'id' => $activity['category_id'],
        ]);
    }

    #[Route('/new/category/{id}', name: 'activity_new')]
    #[IsGranted(CategoryVoter::MANAGE, subject: 'id')]
    public function new(int $id, Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $name = $request->request->get('name');
            $unit = (int) $request->request->get('unit');
            $goal = (int) $request->request->get('goal');
            $isDescriptionEnabled = (bool) $request->request->has('description_enabled');

            $unit = ActivityUnit::from($unit);
            $this->activityService->create(
                new AtivityUpsertDto(
                    $name,              
                    $id,
                    $unit, 
                    $goal, 
                    $isDescriptionEnabled
                )
            );

            $this->goalService->create($id, $goal);

            return $this->redirectToRoute('category_view', [
                'id' => $id,
            ]);
        }

        $category = $this->categoryRepository->getCategoryById($id);

        return $this->render('activity/new.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/update/{id}', name: 'activity_update')]
    #[IsGranted(ActivityVoter::MANAGE, subject: 'id')]
    public function update(int $id, Request $request): Response
    {
        $user = $this->getUser();

        $activity = $this->activityRepository->getActivityById($id);
        $categories = $this->categoryRepository->getUserCategoriesSortedByLastRecord($user->getId());


        if ($request->getMethod() === 'POST') {
            $name = $request->request->get('name');
            $categoryId = (int) $request->request->get('category_id');
            $unit = (int) $request->request->get('unit');
            $goal = (int) $request->request->get('goal');
            $isDescriptionEnabled = (bool) $request->request->has('description_enabled');

            $unit = ActivityUnit::from($unit);
            $this->activityService->update(
                $id,
                new AtivityUpsertDto(
                    $name,              
                    $categoryId, 
                    $unit, 
                    $goal, 
                    $isDescriptionEnabled
                )
            );

            $this->goalService->create($id, $goal);

            return $this->redirectToRoute('activity_view', [
                'id' => $id,
            ]);
        }

        $category = $this->categoryRepository->getCategoryById($activity['category_id']);

        return $this->render('activity/update.html.twig', [
            'category' => $category,
            'activity' => $activity,
            'categories' => $categories,
        ]);
    }
}
