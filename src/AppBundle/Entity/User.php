<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Interfaces\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("p8_user")
 * @ORM\Entity
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, unique=true)
     *
     * @Assert\NotBlank(message="Vous devez saisir une adresse email.")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte.")
     */
    private $email;

    /**
     * User constructor.
     * @param string $username
     * @param string $password
     * @param array $roles
     * @param string $email
     */
    public function __construct(
        string $username,
        string $password,
        array $roles,
        string $email
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->email = $email;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param $password
     *
     * @return void
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @param $username
     *
     * @return void
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @param $email
     *
     * @return void
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return void
     */
    public function eraseCredentials(): void
    {
    }
}
