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

    public function buildTaskListResponse(TaskCollection $tasks): array
    {
        $taskListResponse = [];
        foreach ($tasks as $task) {
            if ($task->isNamespace()) {
                continue;
            }
            $taskListResponse[] = $this->buildTaskResponse($task);
        }
        return $taskListResponse;
    }

    public function buildTaskJsonResponse(Task $task): JsonResponse
    {
        return $this->jsonResponseBuilder->build($this->buildTaskResponse($task));
    }

    private function buildTaskResponse(Task $task): array
    {
        $reminder = $task->getReminder();
        $createdAt = $task->getCreatedAt();
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'parent' => $this->getParentId($task),
            'link' => $task->getLink(),
            'reminder' => $reminder?->getTimestamp(),
            'createdAt' => $createdAt?->getTimestamp(),
            'status' => $task->getStatus()
        ];
    }

    private function getParentId(Task $task): ?int
    {
        if ($task->getLvl() < 2) {
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

    public function buildParentResponse(Task $parent): ?array
    {
        if ($parent->isNamespace()) {
            return null;
        }
        return [
            'id' => $parent->getId(),
            'title' => $parent->getTitle(),
            'parent' => $this->getParentId($parent)
        ];
    }
}
