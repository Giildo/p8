<?php

namespace AppBundle\Entity\Builders\Interfaces;

use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use AppBundle\Entity\Interfaces\TaskInterface;

interface TaskBuilderInterface
{
    public function build(
        TaskDTOInterface $dto,
        ?TaskInterface $task = null
    ): self ;

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface;
}