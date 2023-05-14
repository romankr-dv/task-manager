<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Builder\TaskResponseBuilder;
use App\Collection\TaskCollection;
use App\Config\TaskConfig;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseComposer
{
    public function __construct(
        private TaskResponseBuilder $taskResponseBuilder,
        private TaskRepository $taskRepository,
        private TaskStatusConfig $taskStatusConfig,
        private JsonResponseBuilder $jsonResponseBuilder
    ) {}

    public function composeListResponse(TaskCollection $tasks, Task $parent, int $startFrom): JsonResponse
    {
        $taskListResponse = $this->taskResponseBuilder->buildTaskListResponse($tasks);
        $nextStartFrom = $this->getNextStartFrom($tasks, $startFrom);
        if ($startFrom > 0) {
            return $this->jsonResponseBuilder->build([
                'tasks' => $taskListResponse,
                'startFrom' => $nextStartFrom
            ]);
        }
        $reminderNumber = $this->taskRepository->countTaskReminders($parent);
        $statusCollection = $this->taskStatusConfig->getStatusCollection();
        return $this->jsonResponseBuilder->build([
            'statuses' => $this->taskResponseBuilder->buildStatusListResponse($statusCollection),
            'tasks' => $taskListResponse,
            'reminderNumber' => $reminderNumber,
            'parent' => $this->taskResponseBuilder->buildParentResponse($parent),
            'namespace' => $this->taskResponseBuilder->buildNamespaceResponse($parent->getRoot()),
            'startFrom' => $nextStartFrom
        ]);
    }

    public function composeTaskResponse(Task $task): JsonResponse
    {
        return $this->taskResponseBuilder->buildTaskJsonResponse($task);
    }

    private function getNextStartFrom(TaskCollection $tasks, int $startFrom): ?int
    {
        $limit = TaskConfig::LIMIT_PER_REQUEST;
        if ($tasks->count() < $limit) {
            return null;
        }
        return $startFrom + $limit;
    }
}
