<?php

namespace App\Controller;

use App\Config\TaskConfig;
use App\Config\TaskStatusConfig;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/tasks/report')]
class TaskReportController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskStatusConfig $taskStatusConfig
    ) {}

    #[Route('', name: 'task-report-index', methods: ['GET'])]
    public function index(): Response
    {
        $limit = TaskConfig::LIMIT_PER_REQUEST;
        $parent = $this->taskRepository->findUserRootTask($this->getUser());
        $status = $this->taskStatusConfig->getStatusById(TaskStatusConfig::IN_PROGRESS_STATUS_ID);
        $tasks = $this->taskRepository->findTasksByStatus($parent, $status, '', 0, $limit);
        return $this->render('report/index.html.twig', ['tasks' => $tasks]);
    }
}
