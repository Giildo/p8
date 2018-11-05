<?php

namespace AppBundle\Controller;

use AppBundle\Form\ConnectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route(
     *     path="/login",
     *     name="login"
     * )
     *
     * @return Response
     */
    public function loginAction(): Response
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(ConnectionType::class);

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
                'form'          => $form->createView(),
            ]
        );
    }
}
