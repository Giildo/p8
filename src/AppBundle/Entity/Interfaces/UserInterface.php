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
    public function setPassword($password): void;

    /**
     * @param $username
     *
     * @return void
     */
    public function setUsername($username): void;

    /**
     * @param $email
     *
     * @return void
     */
    public function setEmail($email): void;
}
