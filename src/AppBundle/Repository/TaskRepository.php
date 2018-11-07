<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Interfaces\TaskInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class TaskRepository extends EntityRepository
{
    /**
     * @return array|TaskInterface[]
     */
    public function findAllTasks(): array
    {
        return $this->createQueryBuilder('t')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param int $id
     *
     * @return TaskInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneTaskById(int $id): ?TaskInterface
    {
        return $this->createQueryBuilder('t')
                    ->where('t.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
