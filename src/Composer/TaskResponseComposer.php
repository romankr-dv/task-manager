<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Builder\TaskResponseBuilder;
use App\Collection\TaskCollection;
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

    public function composeListResponse(TaskCollection $tasks, Task $parent): JsonResponse
    {
        $reminderNumber = $this->taskRepository->countTaskReminders($parent);
        $statusCollection = $this->taskStatusConfig->getStatusCollection();
        return $this->jsonResponseBuilder->build([
            'statuses' => $this->taskResponseBuilder->buildStatusListResponse($statusCollection),
            'tasks' => $this->taskResponseBuilder->buildTaskListResponse($tasks),
            'reminderNumber' => $reminderNumber,
            'parent' => $this->taskResponseBuilder->buildParentResponse($parent),
            'namespace' => $this->taskResponseBuilder->buildNamespaceResponse($parent->getRoot())
        ]);
    }

    public function composeTaskResponse(Task $task): JsonResponse
    {
        return $this->taskResponseBuilder->buildTaskJsonResponse($task);
    }
}
