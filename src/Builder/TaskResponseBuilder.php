<?php

namespace App\Builder;

use App\Collection\TaskCollection;
use App\Collection\TaskStatusCollection;
use App\Entity\Task;
use App\Entity\TaskStatus;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilder
{
    public function __construct(
        private JsonResponseBuilder $jsonResponseBuilder
    ) {}

    public function buildStatusListResponse(TaskStatusCollection $collection): array
    {
        $statusListResponse = [];
        foreach ($collection as $status) {
            $statusListResponse[] = $this->buildStatusResponse($status);
        }
        return $statusListResponse;
    }

    public function buildTaskListResponse(
        TaskCollection $tasks,
        Task $root
    ): array {
        $taskListResponse = [];
        foreach ($tasks as $task) {
            if ($task->getParent() === null) {
                continue;
            }
            $taskListResponse[] = $this->buildTaskResponse($task, $root);
        }
        return $taskListResponse;
    }

    public function buildTaskJsonResponse(Task $task, Task $root): JsonResponse
    {
        return $this->jsonResponseBuilder->build($this->buildTaskResponse($task, $root));
    }

    private function buildTaskResponse(Task $task, Task $root): array
    {
        $reminder = $task->getReminder();
        $createdAt = $task->getCreatedAt();
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'parent' => $this->getParentId($task, $root),
            'link' => $task->getLink(),
            'reminder' => $reminder?->getTimestamp(),
            'createdAt' => $createdAt?->getTimestamp(),
            'status' => $task->getStatus()
        ];
    }

    private function getParentId(Task $task, Task $root): ?int
    {
        if (null === $task->getParent()) {
            return null;
        }
        if ($task->getParent()->equals($root)) {
            return null;
        }
        return $task->getParent()->getId();
    }

    private function buildStatusResponse(TaskStatus $status): array
    {
        return [
            'id' => $status->getId(),
            'title' => $status->getTitle(),
            'color' => $status->getColor()
        ];
    }

    public function buildParentResponse(Task $parent, Task $root): ?array
    {
        if ($parent->equals($root)) {
            return null;
        }
        return [
            'id' => $parent->getId(),
            'title' => $parent->getTitle(),
            'parent' => $this->getParentId($parent, $root)
        ];
    }
}
