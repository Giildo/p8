<?php

namespace AppBundle\Helpers\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * AccessDeniedHandler constructor.
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        Request $request,
        AccessDeniedException $accessDeniedException
    ) {
        $this->flashBag->set('accessDenied', 'Vous n\'avez pas les droits pour accéder à cette page.');
        return new RedirectResponse('/');
    }
}
