<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Checker\TaskPermissionChecker;
use App\Composer\TrackedPeriodResponseComposer;
use App\Entity\Task;
use App\Repository\TrackedPeriodRepository;
use App\Service\TrackedPeriodService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/internal-api/tasks')]
class TrackedPeriodController extends AbstractController
{
    public function __construct(
        private TrackedPeriodRepository $trackedPeriodRepository,
        private JsonResponseBuilder $jsonResponseBuilder,
        private TaskPermissionChecker $taskPermissionChecker,
        private TrackedPeriodService $trackedPeriodService,
        private TrackedPeriodResponseComposer $responseComposer
    ) {}

    #[Route('/{id}/start', name: 'app_task_start', methods: ['POST'])]
    public function start(Task $task): JsonResponse
    {
        if (!$this->taskPermissionChecker->canTrackTask($this->getUser(), $task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $lastPeriod = $this->trackedPeriodRepository->findLastTrackedPeriod($this->getUser());
        if (!is_null($lastPeriod) && $lastPeriod->isActive()) {
            if ($lastPeriod->getTask()->equals($task)) {
                return $this->jsonResponseBuilder->buildError("Task already active");
            }
            $this->trackedPeriodService->finishPeriod($lastPeriod);
        }
        $this->trackedPeriodService->startPeriod($this->getUser(), $task);
        return $this->responseComposer->compose($task);
    }

    #[Route('/{id}/finish', name: 'app_task_finish', methods: ['POST'])]
    public function finish(Task $task): JsonResponse
    {
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($this->getUser());
        if (is_null($activePeriod) || !$activePeriod->getTask()->equals($task)) {
            return $this->jsonResponseBuilder->buildError("The task can't be finished");
        }
        $this->trackedPeriodService->finishPeriod($activePeriod);
        return $this->jsonResponseBuilder->build();
    }
}
