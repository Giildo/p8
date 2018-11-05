<?php

namespace AppBundle\Entity\Builders;

use AppBundle\Entity\Builders\Interfaces\UserBuilderInterface;
use AppBundle\Entity\DTO\Interfaces\UserDTOInterface;
use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserBuilder implements UserBuilderInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * UserBuilder constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(
        UserDTOInterface $dto,
        ?UserInterface $user = null
    ): UserBuilderInterface {
        if (is_null($user)) {
            $this->user = new User(
                $dto->username,
                $dto->roles,
                $dto->email
            );

            $this->user->setPassword(
                $this->passwordEncoder->encodePassword($this->user, $dto->password)
            );
        } else {
            $user->setUsername($dto->username);
            $user->setEmail($dto->email);
            $user->setRoles([$dto->roles]);

            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $dto->password)
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
