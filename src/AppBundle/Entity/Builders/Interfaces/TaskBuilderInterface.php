<?php

namespace AppBundle\Entity\Builders\Interfaces;

use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use AppBundle\Entity\Interfaces\TaskInterface;
use AppBundle\Entity\Interfaces\UserInterface;

interface TaskBuilderInterface
{
    public function build(
        TaskDTOInterface $dto,
        ?TaskInterface $task = null,
        ?UserInterface $user = null
    ): self ;

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface;
}