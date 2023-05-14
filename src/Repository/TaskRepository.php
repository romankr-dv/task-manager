<?php

namespace App\Repository;

use App\Builder\TaskBuilder;
use App\Collection\TaskCollection;
use App\Collection\TaskStatusCollection;
use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\TreeListener;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Task[]    findChildren(Task $node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
 */
class TaskRepository extends NestedTreeRepository
{
    private TaskBuilder $taskBuilder;

    public function __construct(
        ManagerRegistry $registry,
        TreeListener $treeListener,
        TaskBuilder $taskBuilder
    ) {
        parent::__construct($registry, Task::class, $treeListener);
        $this->taskBuilder = $taskBuilder;
    }

    private function prepareUserTasksQueryBuilder(Task $parent): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t');
        if (!$parent->isNamespace()) {
            $queryBuilder->andWhere("t.parent = :parent");
            $queryBuilder->setParameter('parent', $parent);
        }
        return $queryBuilder->orderBy("t.id", "DESC");
    }

    public function findTasks(Task $parent): TaskCollection
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($parent);
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function findUserReminders(Task $parent): TaskCollection
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($parent);
        $queryBuilder->andWhere("t.reminder < :time");
        $queryBuilder->setParameter('time', new DateTime());
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function countUserReminders(User $user): int
    {
        $parent = $this->findUserRootTask($user);
        return $this->countTaskReminders($parent);
    }

    public function countTaskReminders(Task $parent): int
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($parent);
        $queryBuilder->andWhere("t.reminder < :time");
        $queryBuilder->setParameter('time', new DateTime());
        $queryBuilder->select("count(t.id)");
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function findUserTasksByStatusList(Task $parent, TaskStatusCollection $taskStatusCollection): TaskCollection
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($parent);
        $queryBuilder->andWhere("t.status IN (:statusList)");
        $queryBuilder->setParameter('statusList', $taskStatusCollection->getIds());
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function findUserTasksByStatus(Task $parent, TaskStatus $status): TaskCollection
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($parent);
        $queryBuilder->andWhere("t.status = :status");
        $queryBuilder->setParameter('status', $status->getId());
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function findUserRootTask(User $user): Task
    {
        $root = $this->findOneBy(['user' => $user, 'parent' => null]);
        return $root ?? $this->createRootTask($user);
    }

    private function createRootTask(User $user): Task
    {
        $root = $this->taskBuilder->buildRootTask($user);
        $this->_em->persist($root);
        $this->_em->flush();
        return $root;
    }

    public function getTaskPath(Task $task): TaskCollection
    {
        $nodes = $this->getPath($task);
        return new TaskCollection($nodes);
    }
}
