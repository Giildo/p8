<?php

namespace AppBundle\Entity\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface as UserInterfaceSecurity;

interface UserInterface extends UserInterfaceSecurity
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param $password
     *
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * @param $username
     *
     * @return void
     */
    public function setUsername(string $username): void;

    /**
     * @param $email
     *
     * @return void
     */
    public function setEmail(string $email): void;

    /**
     * @param array $roles
     *
     * @return void
     */
    public function setRoles(array $roles): void;
}
