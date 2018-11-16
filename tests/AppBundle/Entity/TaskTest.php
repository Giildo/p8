<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ToolsException
     */
    public function testGetters()
    {
        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema(
            $entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );
        $schemaTool->createSchema(
            $entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );

        $user = new User(
            'JohnDoe',
            'ROLE_ADMIN',
            'john@doe.fr'
        );
        $user->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $task = new Task(
            'Title',
            'Content',
            $user
        );

        $entityManager->persist($user);
        $entityManager->persist($task);
        $entityManager->flush();

        self::assertEquals(1, $task->getId());
        self::assertInstanceOf(UserInterface::class, $task->getUser());
        self::assertEquals(1, $task->getUser()->getId());
    }

}
