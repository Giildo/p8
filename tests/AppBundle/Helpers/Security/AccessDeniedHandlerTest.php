<?php

namespace Tests\AppBundle\Helpers\Security;

use AppBundle\Helpers\Security\AccessDeniedHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedHandlerTest extends TestCase
{
    public function testTheRedirectResponseOnTheHandler()
    {
        $flashBag = new FlashBag();
        $handler = new AccessDeniedHandler(
            $flashBag
        );

        self::assertInstanceOf(
            RedirectResponse::class,
            $handler->handle(
                new Request(),
                $this->createMock(AccessDeniedException::class)
            )
        );
        self::assertEquals(
            'Vous n\'avez pas les droits pour accéder à cette page.',
            $flashBag->get('accessDenied')[0]
        );
    }
}
