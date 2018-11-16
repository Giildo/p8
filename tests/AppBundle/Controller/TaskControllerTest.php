<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\TaskController;
use AppBundle\Entity\Builders\TaskBuilder;
use AppBundle\Entity\DTO\TaskDTO;
use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class TaskControllerTest extends KernelTestCase
{
    /**
     * @var TaskController
     */
    private $taskController;
    /**
     * @var FormInterface|MockObject
     */
    private $form;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AuthorizationCheckerInterface|MockObject
     */
    private $authorizationChecker;
    /**
     * @var UserInterface
     */
    private $user1;
    /**
     * @var UserInterface
     */
    private $user2;
    /**
     * @var TokenInterface|MockObject
     */
    private $token;

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ToolsException
     */
    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
                                      ->get('doctrine.orm.entity_manager');

        $twig = $this->createMock(Environment::class);
        $twig->method('render')
             ->willReturn('view');

        $this->form = $this->createMock(FormInterface::class);
        $this->form->method('handleRequest')
                   ->willReturnSelf();
        $this->form->method('createView')
                   ->willReturn(
                       $this->createMock(FormView::class)
                   );
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('create')
                    ->willReturn($this->form);

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema(
            $this->entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );
        $schemaTool->createSchema(
            $this->entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );

        $this->user1 = new User(
            'JohnDoe',
            'ROLE_ADMIN',
            'john@doe.fr'
        );
        $this->user1->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $task1 = new Task(
            'Title',
            'Content',
            $this->user1
        );

        $this->user2 = new User(
            'JaneDoe',
            'ROLE_USER',
            'jane@doe.fr'
        );
        $this->user2->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');
        $task2 = new Task(
            'Title 2',
            'Content 2',
            $this->user2
        );

        $task3 = new Task(
            'Title 2',
            'Content 2'
        );

        $this->entityManager->persist($this->user1);
        $this->entityManager->persist($this->user2);
        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->persist($task3);
        $this->entityManager->flush();

        $this->token = $this->createMock(TokenInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
                     ->willReturn($this->token);

        $taskBuilder = new TaskBuilder();

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
                     ->willReturn('/url');

        $flashBag = $this->createMock(FlashBagInterface::class);

        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->taskController = new TaskController(
            $twig,
            $formFactory,
            $this->entityManager,
            $tokenStorage,
            $taskBuilder,
            $urlGenerator,
            $flashBag,
            $this->authorizationChecker
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testListAction()
    {
        self::assertInstanceOf(
            Response::class,
            $this->taskController->listAction()
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testCreateActionReturnResponseIfFormIsNotSubmitted()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(false);

        $response = $this->taskController->createAction(new Request());
        self::assertInstanceOf(
            Response::class,
            $response
        );
        self::assertNotInstanceOf(
            RedirectResponse::class,
            $response
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testCreateActionReturnResponseIfFormIsSubmittedAndIsNotValid()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(false);

        $response = $this->taskController->createAction(new Request());
        self::assertInstanceOf(
            Response::class,
            $response
        );
        self::assertNotInstanceOf(
            RedirectResponse::class,
            $response
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NonUniqueResultException
     */
    public function testCreateActionReturnResponseIfFormIsSubmittedAndIsValid()
    {
        $dto = new TaskDTO(
            'Title perso',
            'Content'
        );

        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(true);
        $this->form->method('getData')
                   ->willReturn($dto);

        $this->token->method('getUser')
                    ->willReturn($this->user1);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->createAction(new Request())
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(4);

        self::assertEquals(
            'Title perso',
            $task->getTitle()
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NonUniqueResultException
     */
    public function testEditActionRedirectResponseReturnIfIdIsWrong()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->editAction(
                100,
                new Request()
            )
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testEditActionResponseReturnIfIdIsCorrectAndIfFormIsNotSubmitted()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(false);

        $response = $this->taskController->editAction(
            1,
            new Request()
        );
        self::assertInstanceOf(
            Response::class,
            $response
        );
        self::assertNotInstanceOf(
            RedirectResponse::class,
            $response
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testEditActionResponseReturnIfIdIsCorrectAndIfFormIsSubmittedAndIsNotValid()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(false);

        $response = $this->taskController->editAction(
            1,
            new Request()
        );
        self::assertInstanceOf(
            Response::class,
            $response
        );
        self::assertNotInstanceOf(
            RedirectResponse::class,
            $response
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testEditActionRedirectResponseReturnIfIdIsCorrectAndIfFormIsSubmittedAndIsValid()
    {
        $dto = new TaskDTO(
            'New title',
            'Content'
        );

        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(true);
        $this->form->method('getData')
                   ->willReturn($dto);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->editAction(
                1,
                new Request()
            )
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(1);

        self::assertEquals(
            'New title',
            $task->getTitle()
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testToggleActionRedirectResponseIfIdIsWrong()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->toggleTaskAction(1)
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(1);

        self::assertTrue($task->isDone());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testToggleActionRedirectResponseIfIdIsCorrect()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->toggleTaskAction(100)
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(1);

        self::assertFalse($task->isDone());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testDeleteActionRedirectResponseIfIdIsWrong()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->deleteTaskAction(100)
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(1);

        self::assertEquals(
            'Title',
            $task->getTitle()
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testDeleteActionRedirectResponseReturnIfTheUserHasNotTheRights()
    {
        $this->token->method('getUser')
                    ->willReturn($this->user2);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->deleteTaskAction(1)
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(1);

        self::assertEquals(
            'Title',
            $task->getTitle()
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testDeleteActionResponseReturnIfIdIsCorrect()
    {
        $this->token->method('getUser')
                    ->willReturn($this->user1);

        $this->authorizationChecker->method('isGranted')
                                   ->willReturn(true);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->taskController->deleteTaskAction(3)
        );

        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById(3);

        self::assertNull($task);
    }
}
