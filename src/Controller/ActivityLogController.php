<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\DateFromToDto;
use App\Service\RecordService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('activity-log')]
#[IsGranted('IS_AUTHENTICATED')]
final class ActivityLogController extends AbstractController
{
    public function __construct(
        private readonly RecordService $recordService,
    ) {}

    #[Route('', name: 'activity_log_index')]
    public function index(Request $request): Response
    {
        $filter = $request->request->get('filter', RecordService::FILTER_PERIOD_TODAY);

        if ($filter != 'own') {
            $filterDates = $this->recordService->getDateRange($filter);
        } else {
            $filterDates = new DateFromToDto(
                dateFrom: new DateTimeImmutable($request->request->get('dateFrom', RecordService::FILTER_PERIOD_TODAY)),
                dateTo: new DateTimeImmutable($request->request->get('dateTo', RecordService::FILTER_PERIOD_TODAY)),
            );
        }
        
        $user = $this->getUser();
        $records = $this->recordService->getRecords($user, $filterDates);
        $recordSums = $this->recordService->getRecordsSum($user, $filterDates);

        return $this->render('activity-log/index.html.twig', [
            'buttons' => RecordService::RECORD_FILTERS,
            'recordSums' => $recordSums,
            'records' => $records,
            'filter' => $filter,
            'dateTo' => $filterDates->dateTo->format('Y-m-d'),
            'dateFrom' => $filterDates->dateFrom->format('Y-m-d'),
        ]);
    }
}
