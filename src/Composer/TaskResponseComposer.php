<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Builder\TaskResponseBuilder;
use App\Collection\TaskCollection;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
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

    public function composeListResponse(User $user, TaskCollection $tasks, Task $parent): JsonResponse
    {
        $root = $this->taskRepository->findUserRootTask($user);
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        $statusCollection = $this->taskStatusConfig->getStatusCollection();
        return $this->jsonResponseBuilder->build([
            'statuses' => $this->taskResponseBuilder->buildStatusListResponse($statusCollection),
            'tasks' => $this->taskResponseBuilder->buildTaskListResponse($tasks, $root),
            'reminderNumber' => $reminderNumber,
            'parent' => $this->taskResponseBuilder->buildParentResponse($parent, $root)
        ]);
    }

    public function composeTaskResponse(User $user, Task $task): JsonResponse
    {
        $root = $this->taskRepository->findUserRootTask($user);
        return $this->taskResponseBuilder->buildTaskJsonResponse($task, $root);
    }
}
