<?php

namespace App\Composer;

use App\Builder\JsonResponseBuilder;
use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class SettingsResponseComposer
{
    public function __construct(
        private TaskRepository $taskRepository,
        private JsonResponseBuilder $jsonResponseBuilder
    ) {}

    public function composeListResponse(User $user): JsonResponse
    {
        $reminderNumber = $this->taskRepository->countUserReminders($user);
        return $this->jsonResponseBuilder->build([
            'reminderNumber' => $reminderNumber
        ]);
    }
}
