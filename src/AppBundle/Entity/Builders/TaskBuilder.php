<?php

namespace AppBundle\Entity\Builders;

use AppBundle\Entity\Builders\Interfaces\TaskBuilderInterface;
use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use AppBundle\Entity\Interfaces\TaskInterface;
use AppBundle\Entity\Task;

class TaskBuilder implements TaskBuilderInterface
{
    /**
     * @var TaskInterface
     */
    private $task;

    public function build(
        TaskDTOInterface $dto,
        ?TaskInterface $task = null
    ): TaskBuilderInterface {
        if (is_null($task)) {
            $this->task = new Task(
                $dto->title,
                $dto->content
            );
        } else {
            $task->setContent($dto->content);
            $task->setTitle($dto->title);
        }

        return $this;
    }

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface
    {
        return $this->task;
    }
}
