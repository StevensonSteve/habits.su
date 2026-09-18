<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\ActivityUnit;
use App\Repository\ActivityRepository;
use App\Repository\RecordRepository;
use App\Security\ActivityVoter;
use App\Security\RecordVoter;
use App\Service\RecordService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/record')]
#[IsGranted('IS_AUTHENTICATED')]
final class RecordController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
        private readonly RecordService $recordService,
    ) {}

    #[Route('/new/activity/{id}', name: 'record_new')]
    #[IsGranted(ActivityVoter::MANAGE, subject: 'id')]
    public function new(int $id, Request $request): Response
    {
        $amount = (float) $request->request->get('amount', 0);
        $date = $request->request->get('date', 0);

        $activity = $this->activityRepository->getActivityById($id);

        $createdAt = $date
            ? (new DateTimeImmutable($date . ' ' . date('H:i:s')))->format("Y-m-d H:i:s")
            : (new DateTimeImmutable())->format("Y-m-d H:i:s");

        $today = (new DateTimeImmutable())->format("Y-m-d H:i:s");
        // ToDo сделать нормальную валидацию
        if ($createdAt > $today) {
            $this->addFlash(
                'error',
                'Дата не должна быть в будущем!',
            );
        } elseif (
            (
                (fmod($amount, 1.0) === 0.0 && $activity['unit'] !== ActivityUnit::KILOMETERS->value)
                || $activity['unit'] === ActivityUnit::KILOMETERS->value
            ) && $amount > 0.0
        ) {
            $this->recordService->create($amount, $id, $createdAt);

            $this->addFlash(
                'success',
                'Создана запись: ' . $activity['name'] . ' · ' . $amount . ' '
                . ActivityUnit::from($activity['unit'])->label(),
            );
        } else {
            $this->addFlash(
                'error',
                'Значение должно быть целым положительным числом!',
            );
        }

        if (! $date) {
            return $this->redirectToRoute('category_view', [
                'id' => $activity['category_id'],
            ]);
        } else {
            return $this->redirectToRoute('activity_view', [
                'id' => $activity['id'],
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'record_delete')]
    #[IsGranted(RecordVoter::MANAGE, subject: 'id')]
    public function delete(int $id): Response
    {
        $activityId = $this->recordRepository->getActivityId($id);
        $this->recordService->delete($id);

        return $this->redirectToRoute('activity_view', [
            'id' => $activityId,
        ]);
    }
}
