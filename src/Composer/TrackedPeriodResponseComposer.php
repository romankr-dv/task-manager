<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TrackedPeriodResponseComposer
{
    public function __construct(
        private TaskRepository $taskRepository,
        private JsonResponseBuilder $jsonResponseBuilder
    ) {}

    public function compose(Task $task): JsonResponse
    {
        $path = $this->taskRepository->getTaskPath($task);
        return $this->jsonResponseBuilder->build(['activeTask' => [
            'path' => $path->getIds()
        ]]);
    }
}
