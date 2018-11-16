<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\UserController;
use AppBundle\Entity\Builders\UserBuilder;
use AppBundle\Entity\DTO\RegistrationDTO;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class UserControllerTest extends KernelTestCase
{
    /**
     * @var UserController
     */
    private $userController;
    /**
     * @var FormInterface|MockObject
     */
    private $form;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema(
            $this->entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );
        $schemaTool->createSchema(
            $this->entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );

        $user = new User(
            'JohnDoe',
            'ROLE_ADMIN',
            'john@doe.fr'
        );
        $user->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $twig = $this->createMock(Environment::class);
        $twig->method('render')
             ->willReturn('view');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
                     ->willReturn('/url');

        $flashBag = $this->createMock(FlashBagInterface::class);

        $this->form = $this->createMock(FormInterface::class);
        $this->form->method('handleRequest')
                   ->willReturnSelf();
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('create')
                    ->willReturn($this->form);

        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $passwordEncoder->method('encodePassword')
                        ->willReturn('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');
        $userBuilder = new UserBuilder(
            $passwordEncoder
        );

        $this->userController = new UserController(
            $twig,
            $urlGenerator,
            $flashBag,
            $this->entityManager,
            $formFactory,
            $userBuilder
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testListAction()
    {
        $response = $this->userController->listAction();
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
    public function testCreateActionResponseReturnIfFormIsNotSubmitted()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(false);

        $response = $this->userController->createAction(new Request());
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
    public function testCreateActionResponseReturnIfFormIsSubmittedAndIsNotValid()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(false);

        $response = $this->userController->createAction(new Request());
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
    public function testCreateActionRedirectResponseReturnIfFormIsSubmittedAndIsValid()
    {
        $dto = new RegistrationDTO(
            'JaneDoe',
            '12345678',
            'ROLE_ADMIN',
            'jane@doe.fr'
        );
        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(true);
        $this->form->method('getData')
                   ->willReturn($dto);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->userController->createAction(new Request())
        );

        $user = $this->entityManager->getRepository(User::class)
                                    ->findUserById(2);

        self::assertEquals('JaneDoe', $user->getUsername());
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NonUniqueResultException
     */
    public function testEditActionRedirectResponseReturnIfIdIsWrong()
    {
        $response = $this->userController->editAction(
            100,
            new Request()
        );
        self::assertInstanceOf(
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
    public function testEditActionResponseReturnIfFormIsNotSubmitted()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(false);

        $response = $this->userController->editAction(
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
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NonUniqueResultException
     */
    public function testEditActionResponseReturnIfFormIsSubmittedAndIsNotValid()
    {
        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(false);

        $response = $this->userController->editAction(
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
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws NonUniqueResultException
     */
    public function testEditActionRedirectResponseReturnIfFormIsSubmittedAndIsValid()
    {
        $dto = new RegistrationDTO(
            'JaneDoe',
            '12345678',
            'ROLE_ADMIN',
            'jane@doe.fr'
        );
        $this->form->method('isSubmitted')
                   ->willReturn(true);
        $this->form->method('isValid')
                   ->willReturn(true);
        $this->form->method('getData')
                   ->willReturn($dto);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->userController->editAction(
                1,
                new Request()
            )
        );

        $user = $this->entityManager->getRepository(User::class)
                                    ->findUserById(1);

        self::assertEquals(
            'JaneDoe',
            $user->getUsername()
        );
    }
}
