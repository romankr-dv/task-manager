<?php

namespace App\Composer;

use App\Builder\HistoryActionResponseBuilder;
use App\Builder\JsonResponseBuilder;
use App\Collection\HistoryActionCollection;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class HistoryResponseComposer
{
    public function __construct(
        private TaskRepository $taskRepository,
        private JsonResponseBuilder $jsonResponseBuilder,
        private HistoryActionResponseBuilder $historyActionResponseBuilder
    ) {}

    public function composeResponse(
        User $user,
        HistoryActionCollection $actions,
        ?Task $task,
        int $startFrom
    ): JsonResponse {
        $nextStartFrom = $this->getNextStartFrom($actions, $startFrom);
        $includeActionTask = $task === null;
        $actionResponse = $this->historyActionResponseBuilder->buildActionListResponse($actions, $includeActionTask);
        if ($startFrom > 0) {
            return $this->jsonResponseBuilder->build([
                'actions' => $actionResponse,
                'startFrom' => $nextStartFrom
            ]);
        }
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        return $this->jsonResponseBuilder->build([
            'actions' => $actionResponse,
            'reminderNumber' => $reminderNumber,
            'task' => $task ? $this->composeTaskResponse($task) : null,
            'startFrom' => $nextStartFrom
        ]);
    }

    private function getNextStartFrom(HistoryActionCollection $actions, int $startFrom): ?int
    {
        if ($actions->isEmpty()) {
            return null;
        }
        return $startFrom + $actions->count();
    }

    private function composeTaskResponse(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle()
        ];
    }
}
