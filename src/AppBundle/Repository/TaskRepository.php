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
        return $this->_em->createQuery("SELECT t FROM AppBundle\Entity\Task t")
                         ->useResultCache(true)
                         ->setResultCacheLifetime(60)
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
                    ->setParameter(
                        'id',
                        $id
                    )
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
