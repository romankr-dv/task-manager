<?php

namespace App\Controller;

use App\Builder\JsonResponseBuilder;
use App\Checker\TaskPermissionChecker;
use App\Composer\TaskResponseComposer;
use App\Config\TaskConfig;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/internal-api/tasks')]
class TaskController extends AbstractController
{
    private const STATUS_REQUEST_FIELD = 'status';

    public function __construct(
        private TaskRepository $taskRepository,
        private TaskResponseComposer $taskResponseComposer,
        private TaskStatusConfig $taskStatusConfig,
        private JsonResponseBuilder $jsonResponseBuilder,
        private TaskService $taskService,
        private TaskPermissionChecker $taskPermissionChecker
    ) {}

    #[Route('', name: 'app_api_task_all', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        $parent = $this->getTaskParentParam($request);
        $startFrom = $this->getStartFromParam($request);
        $search = $this->getSearchParam($request);
        $limit = TaskConfig::LIMIT_PER_REQUEST;
        $tasks = $this->taskRepository->findTasks($parent, $search, $startFrom, $limit);
        return $this->taskResponseComposer->composeListResponse($tasks, $parent, $startFrom);
    }

    #[Route('/reminders', name: 'app_api_task_reminders', methods: ['GET'])]
    public function reminders(Request $request): JsonResponse
    {
        $parent = $this->getTaskParentParam($request);
        $startFrom = $this->getStartFromParam($request);
        $search = $this->getSearchParam($request);
        $limit = TaskConfig::LIMIT_PER_REQUEST;
        $tasks = $this->taskRepository->findTaskReminders($parent, $search, $startFrom, $limit);
        return $this->taskResponseComposer->composeListResponse($tasks, $parent, $startFrom);
    }

    #[Route('/todo', name: 'app_api_task_todo', methods: ['GET'])]
    public function todo(Request $request): JsonResponse
    {
        $parent = $this->getTaskParentParam($request);
        $startFrom = $this->getStartFromParam($request);
        $search = $this->getSearchParam($request);
        $limit = TaskConfig::LIMIT_PER_REQUEST;
        $statusCollection = $this->taskStatusConfig->getTodoStatusCollection();
        $tasks = $this->taskRepository->findTasksByStatusList($parent, $statusCollection, $search, $startFrom, $limit);
        return $this->taskResponseComposer->composeListResponse($tasks, $parent, $startFrom);
    }

    #[Route('/status/{status}', name: 'app_api_task_status', methods: ['GET'])]
    public function status(Request $request): JsonResponse
    {
        $parent = $this->getTaskParentParam($request);
        $startFrom = $this->getStartFromParam($request);
        $search = $this->getSearchParam($request);
        $limit = TaskConfig::LIMIT_PER_REQUEST;
        $statusSlug = $request->attributes->get(self::STATUS_REQUEST_FIELD);
        if (!$this->taskStatusConfig->isStatusSlugExisting($statusSlug)) {
            return $this->jsonResponseBuilder->buildError('Task status not valid');
        }
        $status = $this->taskStatusConfig->getStatusBySlug($statusSlug);
        $tasks = $this->taskRepository->findTasksByStatus($parent, $status, $search, $startFrom, $limit);
        return $this->taskResponseComposer->composeListResponse($tasks, $parent, $startFrom);
    }

    #[Route('/new', name: 'app_api_task_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $parent = $this->getTaskParentParam($request);
        if (null === $parent) {
            return $this->jsonResponseBuilder->buildError('Parent task not found');
        }
        $user = $this->getUser();
        if (!$parent->getUser()->equals($user)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $task = $this->taskService->createTask($user, $parent);
        return $this->taskResponseComposer->composeTaskResponse($task);
    }

    private function getTaskParentParam(Request $request): ?Task
    {
        $parent = $request->request->get('parent', $request->query->get('parent'));
        if (!$parent) {
            return $this->taskRepository->findUserRootTask($this->getUser());
        }
        return $this->taskRepository->findOneBy(['id' => $parent]);
    }

    private function getStartFromParam(Request $request): int
    {
        return (int) max($request->query->get('startFrom'), 0);
    }

    private function getSearchParam(Request $request): string
    {
        return (string) $request->query->get('search');
    }

    #[Route('/{id}/edit', name: 'app_api_task_edit', methods: ['POST'])]
    public function edit(Task $task, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$this->taskPermissionChecker->canEditTask($user, $task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $this->taskService->editTask($user, $task, $request->request);
        return $this->jsonResponseBuilder->build();
    }

    #[Route('/{id}/update-priority', name: 'app_api_task_update_priority', methods: ['POST'])]
    public function updateTaskPriority(Task $task): JsonResponse
    {
        $user = $this->getUser();
        if (!$this->taskPermissionChecker->canEditTask($user, $task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
        $this->taskService->updateTaskPriority($task);
        return $this->jsonResponseBuilder->build();
    }


    #[Route('/{id}/delete', name: 'app_api_task_delete', methods: ['POST'])]
    public function delete(Task $task): JsonResponse
    {
        if (!$this->taskPermissionChecker->canDeleteTask($this->getUser(), $task)) {
            return $this->jsonResponseBuilder->buildPermissionDenied();
        }
//        todo: investigate adding csrf token validation
//        $this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))
        $this->taskService->deleteTask($task);
        return $this->jsonResponseBuilder->build();
    }
}
