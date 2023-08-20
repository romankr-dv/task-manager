<?php

namespace App\Service;

use App\Builder\HistoryActionBuilder;
use App\Builder\TaskBuilder;
use App\Composer\HistoryActionMessageComposer;
use App\Config\HistoryActionConfig;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $entityManager,
        private TaskBuilder $taskBuilder,
        private HistoryActionBuilder $historyActionBuilder,
        private HistoryActionMessageComposer $historyActionMessageComposer
    ) {}

    public function createTask(User $user, Task $parent): Task
    {
        $task = $this->taskBuilder->buildNewTask($user, $parent);
        $this->entityManager->persist($task);

        $message = $this->historyActionMessageComposer->composeNewTaskMessage();
        $this->createHistoryAction($user, $task, HistoryActionConfig::CREATE_TASK_ACTION, $message);
        $this->entityManager->flush();
        return $task;
    }

    public function editTask(User $user, Task $task, ParameterBag $input): void
    {
        if ($input->has('title')) {
            $task->setTitle($input->get('title'));
            $message = $this->historyActionMessageComposer->composeTaskTitleUpdateMessage($task->getTitle());
            $this->createHistoryAction($user, $task, HistoryActionConfig::EDIT_TASK_TITLE_ACTION, $message);
        }
        if ($input->has('link')) {
            $task->setLink($input->get('link'));
            $message = $this->historyActionMessageComposer->composeTaskLinkUpdateMessage($task->getLink());
            $this->createHistoryAction($user, $task, HistoryActionConfig::EDIT_TASK_LINK_ACTION, $message);
        }
        if ($input->has('reminder')) {
            $reminder = $input->get('reminder');
            $task->setReminder($reminder ? (new DateTime())->setTimestamp($reminder) : null);
            $message = $this->historyActionMessageComposer->composeTaskReminderUpdateMessage($task->getReminder());
            $this->createHistoryAction($user, $task, HistoryActionConfig::EDIT_TASK_REMINDER_ACTION, $message);
        }
        if ($input->has('status')) {
            $task->setStatus($input->get('status'));
            $message = $this->historyActionMessageComposer->composeTaskStatusUpdateMessage($task->getStatus());
            $this->createHistoryAction($user, $task, HistoryActionConfig::EDIT_TASK_STATUS_ACTION, $message);
        }
        if ($input->has('description')) {
            $task->setDescription($input->get('description'));
            $message = $this->historyActionMessageComposer->composeTaskDescriptionUpdateMessage($task->getDescription());
            $this->createHistoryAction($user, $task, HistoryActionConfig::EDIT_TASK_DESCRIPTION_ACTION, $message);
        }
        $this->entityManager->flush();
    }

    public function updateTaskPriority(Task $task): void
    {
        $priorityUpdatedAt = new DateTime();
        $task->setPriorityUpdatedAt($priorityUpdatedAt);
        $this->entityManager->flush();
    }

    public function deleteTask(Task $task): void
    {
        $children = $this->taskRepository->findChildren($task);
        foreach ($children as $child) {
            $this->entityManager->remove($child);
        }
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function createHistoryAction(User $user, ?Task $task, string $type, string $message): void
    {
        $historyAction = $this->historyActionBuilder->buildAction($user, $task, $type, $message);
        $this->entityManager->persist($historyAction);
    }
}
