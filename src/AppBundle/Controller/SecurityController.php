<?php

namespace AppBundle\Controller;

use AppBundle\Form\ConnectionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class SecurityController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * SecurityController constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FormFactoryInterface $formFactory
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FormFactoryInterface $formFactory,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route(
     *     path="/login",
     *     name="login"
     * )
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     *
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            $form = $this->formFactory->create(ConnectionType::class);

            return new Response(
                $this->twig->render(
                    'security/login.html.twig',
                    [
                        'last_username' => $lastUsername,
                        'error'         => $error,
                        'form'          => $form->createView(),
                    ]
                )
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('homepage')
        );
    }
}
