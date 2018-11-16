<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class DefaultController extends Controller
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * DefaultController constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route(
     *     path="/",
     *     name="homepage"
     * )
     *
     * @return Response
     *
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function indexAction(): Response
    {
        return new Response(
            $this->twig->render(
                'default/index.html.twig'
            )
        );
    }
}
