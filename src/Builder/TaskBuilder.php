<?php

namespace App\Builder;

use App\Config\TaskConfig;
use App\Entity\Task;
use App\Entity\User;

class TaskBuilder
{
    public function __construct(
        private TaskConfig $taskConfig
    ) {}

    public function buildNewTask(User $user, Task $parent): Task
    {
        $task = new Task();
        $task->setUser($user);
        $task->setTitle($this->taskConfig->getNewTaskTitle());
        $task->setStatus($this->taskConfig->getNewTaskDefaultStatus());
        $task->setParent($parent);
        return $task;
    }

    public function buildRootTask(User $user): Task
    {
        $root = new Task();
        $root->setUser($user);
        $root->setTitle($this->taskConfig->getRootTaskTitle());
        $root->setStatus($this->taskConfig->getRootTaskDefaultStatus());
        return $root;
    }
}
