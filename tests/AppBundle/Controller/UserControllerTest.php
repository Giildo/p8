<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\UserController;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends KernelTestCase
{
    /**
     * @var UserController
     */
    private $userController;

    public function setUp()
    {
        $kernel = self::bootKernel();

        $entityManager = $kernel->getContainer()
                                ->get('doctrine.orm.entity_manager');

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($entityManager->getMetadataFactory()
                                              ->getAllMetadata());
        $schemaTool->createSchema($entityManager->getMetadataFactory()
                                                ->getAllMetadata());

        $user = new User(
            'JohnDoe',
            'ROLE_ADMIN',
            'john@doe.fr'
        );
        $user->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $task = new Task(
            'Titre',
            'Contenu'
        );

        $entityManager->persist($user);
        $entityManager->persist($task);
        $entityManager->flush();

        $this->userController = new UserController();
        $this->userController->setContainer(
            $kernel->getContainer()
        );
    }

    public function testListAction()
    {
        self::assertInstanceOf(
            Response::class,
            $this->userController->listAction()
        );
    }

    public function testCreateAction()
    {
        self::assertInstanceOf(
            Response::class,
            $this->userController->createAction(new Request())
        );
    }

    public function testEditAction()
    {
        self::assertInstanceOf(
            Response::class,
            $this->userController->editAction(1, new Request())
        );
    }
}
