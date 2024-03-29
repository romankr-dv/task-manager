<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Composer\HistoryResponseComposer;
use App\Config\HistoryActionConfig;
use App\Repository\HistoryActionRepository;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/internal-api/history')]
class HistoryController extends AbstractController
{
    public function __construct(
        private HistoryResponseComposer $historyResponseComposer,
        private HistoryActionRepository $historyActionRepository,
        private TaskRepository $taskRepository,
        private JsonResponseBuilder $jsonResponseBuilder
    ) {}

    #[Route('', name: 'app_api_history', methods: ['GET'])]
    public function init(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $limit = HistoryActionConfig::LIMIT_PER_REQUEST;
        $startFrom = (int) max($request->query->get('startFrom'), 0);
        $search = (string) $request->query->get('search');
        $taskId = (int) $request->query->get('task');
        if (empty($taskId)) {
            $actions = $this->historyActionRepository->findByUser($user, $search, $startFrom, $limit);
            return $this->historyResponseComposer->composeResponse($user, $actions, null, $startFrom);
        }
        $task = $this->taskRepository->find($taskId);
        if (empty($task)) {
            return $this->jsonResponseBuilder->buildError("Task not found");
        }
        if (!$task->getUser()->equals($user)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $actions = $this->historyActionRepository->findByTask($task, $search, $startFrom, $limit);
        return $this->historyResponseComposer->composeResponse($user, $actions, $task, $startFrom);
    }
}
