<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\DefaultController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends KernelTestCase
{
    public function testIndex()
    {
        $kernel = self::bootKernel();

        $controller = new DefaultController();
        $controller->setContainer(
            $kernel->getContainer()
        );

        self::assertInstanceOf(Response::class, $controller->indexAction());
    }
}
