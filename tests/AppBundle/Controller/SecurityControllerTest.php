<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\SecurityController;
use AppBundle\Entity\Interfaces\UserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class SecurityControllerTest extends TestCase
{
    /**
     * @var SecurityController
     */
    private $action;

    /**
     * @var AuthenticationUtils|MockObject
     */
    private $authenticationUtils;

    /**
     * @var AuthorizationCheckerInterface|MockObject
     */
    private $authorizationChecker;

    public function setUp()
    {
        $this->authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $this->authenticationUtils->method('getLastAuthenticationError')
                                  ->willReturn(
                                      new AuthenticationException()
                                  );
        $this->authenticationUtils->method('getLastUsername')
                                  ->willReturn(
                                      $this->createMock(UserInterface::class)
                                  );
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $form = $this->createMock(FormInterface::class);
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('create')
                    ->willReturn($form);

        $twig = $this->createMock(Environment::class);
        $twig->method('render')
             ->willReturn('/view');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
                     ->willReturn('/url');

        $this->action = new SecurityController(
            $this->authorizationChecker,
            $formFactory,
            $twig,
            $urlGenerator
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testTheResponseReturn()
    {
        $this->authorizationChecker->method('isGranted')
                                   ->willReturn(false);

        self::assertInstanceOf(
            Response::class,
            $this->action->loginAction(
                $this->authenticationUtils
            )
        );

        self::assertNotInstanceOf(
            RedirectResponse::class,
            $this->action->loginAction(
                $this->authenticationUtils
            )
        );
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function testTheRedirectResponseIfUserIsAlreadyConnected()
    {
        $this->authorizationChecker->method('isGranted')
                                   ->willReturn(true);

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->action->loginAction(
                $this->authenticationUtils
            )
        );
    }
}
