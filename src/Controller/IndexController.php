<?php

namespace App\Controller;

use App\Config\TaskStatusConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class IndexController extends AbstractController
{
    public function __construct(
        private TaskStatusConfig $taskStatusConfig
    ) {}

    #[Route('', name: 'app_index', methods: ['GET'])]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_task_index');
    }

    #[Route('/tasks', name: 'app_task_index', methods: ['GET'])]
    #[Route('/tasks/todo', name: 'app_task_todo', methods: ['GET'])]
    #[Route('/tasks/reminders', name: 'app_task_reminders', methods: ['GET'])]
    #[Route('/{root}/tasks', name: 'app_parent_task_index', requirements: ['root' => '\d+'], methods: ['GET'])]
    #[Route('/{root}/tasks/todo', name: 'app_parent_task_todo', requirements: ['root' => '\d+'], methods: ['GET'])]
    #[Route('/{root}/tasks/reminders', name: 'app_parent_task_reminders', requirements: ['root' => '\d+'], methods: ['GET'])]
    #[Route('/settings', name: 'app_settings', methods: ['GET'])]
    #[Route('/history', name: 'app_history', methods: ['GET'])]
    #[Route('/{task}/history', name: 'app_task_history', requirements: ['root' => '\d+'], methods: ['GET'])]
    public function tasks(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/tasks/status/{status}', name: 'app_task_status', methods: ['GET'])]
    #[Route('/{parent}/tasks/status/{status}', name: 'app_parent_task_status', requirements: ['parent' => '\d+'], methods: ['GET'])]
    public function status(String $status): Response
    {
        if (!$this->taskStatusConfig->isStatusSlugExisting($status)) {
            throw $this->createNotFoundException('Page not found!');
        }
        return $this->render('index.html.twig');
    }
}
