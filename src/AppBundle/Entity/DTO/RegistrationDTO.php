<?php

namespace AppBundle\Entity\DTO;

use AppBundle\Entity\DTO\Interfaces\RegistrationDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDTO implements RegistrationDTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le nom d'utilisateur doit être renseigné")
     * @Assert\Length(
     *     min="2",
     *     minMessage="Le nom d'utilisateur doit comporter au moins {{ limit }} caractères.",
     *     max="25",
     *     maxMessage="Le nom d'utilisateur ne doit pas comporter plus de {{ limit }} caractères."
     * )
     */
    public $username;

    /**
     * @var string
     *
     * @Assert\NotNull(message="Le mot de passe doit être renseigné")
     * @Assert\Length(
     *     min="8",
     *     minMessage="Le mot de passe doit comporter au moins {{ limit }} caractères.",
     *     max="64",
     *     maxMessage="Le mot de passe ne doit pas comporter plus de {{ limit }} caractères."
     * )
     */
    public $password;

    /**
     * @var string
     *?
     *  = null@Assert\NotNull(message="Le rôle de l'utilisateur doit être renseigné.")
     */
    public $roles = [];

    /**
     * @var string
     *
     * @Assert\NotNull(message="L'email doit être renseigné")
     * @Assert\Length(
     *     max="60",
     *     maxMessage="L'email ne doit pas comporter plus de {{ limit }} caractères."
     * )
     * @Assert\Email(message="Le format de l'email n'est pas valide.")
     */
    public $email;

    /**
     * RegistrationDTO constructor.
     * @param string $username
     * @param string $password
     * @param string $roles
     * @param string $email
     */
    public function __construct(
        ?string $username = null,
        ?string $password = null,
        ?string $roles = null,
        ?string $email = null
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->email = $email;
    }
}
