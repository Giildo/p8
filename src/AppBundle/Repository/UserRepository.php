<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Interfaces\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class UserRepository extends EntityRepository
{
    /**
     * @param string $username
     *
     * @return UserInterface
     *
     * @throws NonUniqueResultException
     */
    public function findUserByUsername(string $username): UserInterface
    {
        return $this->createQueryBuilder('u')
                    ->where('u.username = :username')
                    ->setParameter(
                        'username',
                        $username
                    )
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * @param int $id
     *
     * @return UserInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findUserById(int $id): ?UserInterface
    {
        return $this->createQueryBuilder('u')
                    ->where('u.id = :id')
                    ->setParameter(
                        'id',
                        $id
                    )
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * @return UserInterface[]|array
     */
    public function findAllUsers(): array
    {
        return $this->createQueryBuilder('u')
                    ->getQuery()
                    ->getResult();
    }
}
