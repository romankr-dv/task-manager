<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Builder\TaskResponseBuilder;
use App\Collection\TaskCollection;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\TrackedPeriodRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseComposer
{
    public function __construct(
        private TaskResponseBuilder $taskResponseBuilder,
        private TaskRepository $taskRepository,
        private TrackedPeriodRepository $trackedPeriodRepository,
        private TaskStatusConfig $taskStatusConfig,
        private JsonResponseBuilder $jsonResponseBuilder
    ) {}

    public function composeListResponse(User $user, TaskCollection $tasks): JsonResponse
    {
        $root = $this->findRootTask($user, $tasks);
        $activePeriod = $this->trackedPeriodRepository->findActivePeriod($user);
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        $statusCollection = $this->taskStatusConfig->getStatusCollection();
        $activeTask = null;
        if ($activePeriod) {
            $path = $this->taskRepository->getTaskPath($activePeriod->getTask());
            $activeTask = $this->taskResponseBuilder->buildActiveTaskResponse($activePeriod, $path);
        }
        return $this->jsonResponseBuilder->build([
            'statuses' => $this->taskResponseBuilder->buildStatusListResponse($statusCollection),
            'tasks' => $this->taskResponseBuilder->buildTaskListResponse($tasks, $root),
            'activeTask' => $activeTask,
            'reminderNumber' => $reminderNumber
        ]);
    }

    public function composeTaskResponse(User $user, Task $task): JsonResponse
    {
        $root = $this->taskRepository->findUserRootTask($user);
        return $this->taskResponseBuilder->buildTaskJsonResponse($task, $root);
    }

    private function findRootTask(User $user, TaskCollection $tasks): Task
    {
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                return $task;
            }
        }
        return $this->taskRepository->findUserRootTask($user);
    }
}
