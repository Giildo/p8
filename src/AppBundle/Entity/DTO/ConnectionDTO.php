<?php

namespace AppBundle\Entity\DTO;

use AppBundle\Entity\DTO\Interfaces\ConnectionDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ConnectionDTO implements ConnectionDTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le nom d'utilisateur doit être renseigné.")
     */
    public $username;

    /**
     * @var string
     *
     * @Assert\NotNull(message="Le mot de passe doit être renseigné.")
     */
    public $password;

    /**
     * ConnectionDTO constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $username,
        string $password
    ) {
        $this->username = $username;
        $this->password = $password;
    }
}
