<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\DefaultController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DefaultControllerTest extends TestCase
{
    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testIndex()
    {
        $twig = $this->createMock(Environment::class);
        $twig->method('render')
             ->willReturn('view');

        $controller = new DefaultController($twig);

        self::assertInstanceOf(
            Response::class,
            $controller->indexAction()
        );
    }
}
