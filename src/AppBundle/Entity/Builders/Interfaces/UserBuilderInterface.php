<?php

namespace AppBundle\Entity\Builders\Interfaces;

use AppBundle\Entity\DTO\Interfaces\UserDTOInterface;
use AppBundle\Entity\Interfaces\UserInterface;

interface UserBuilderInterface
{
    /**
     * @param UserDTOInterface $dto
     *
     * @param UserInterface|null $user
     * @return UserBuilderInterface
     */
    public function build(
        UserDTOInterface $dto,
        ?UserInterface $user = null
    ): self;

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;
}