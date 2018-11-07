<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\TaskController;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends KernelTestCase
{
    /**
     * @var TaskController
     */
    private $taskController;

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

        $this->taskController = new TaskController();
        $this->taskController->setContainer(
            $kernel->getContainer()
        );
    }

    public function testListAction()
    {
        self::assertInstanceOf(Response::class, $this->taskController->listAction());
    }

    public function testCreateActionReturnResponseIfFormIsNotSubmitted()
    {
        $request = new Request();

        self::assertInstanceOf(
            Response::class,
            $this->taskController->createAction($request)
        );
    }

    public function testEditAction()
    {
        $request = new Request();

        self::assertInstanceOf(
            Response::class,
            $this->taskController->editAction(1, $request)
        );
    }

    public function testRedirectionIfTheIdOfTaskIsWrong()
    {
        $request = new Request();

        self::assertInstanceOf(
            Response::class,
            $this->taskController->editAction(20, $request)
        );
    }

    public function testToggleAction()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->toggleTaskAction(1)
        );
    }

    public function testDeleteAction()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->deleteTaskAction(1)
        );
    }
}
