<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Builders\Interfaces\UserBuilderInterface;
use AppBundle\Entity\DTO\RegistrationDTO;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class UserController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var UserBuilderInterface
     */
    private $userBuilder;

    /**
     * UserController constructor.
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param FlashBagInterface $flashBag
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param UserBuilderInterface $userBuilder
     */
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        UserBuilderInterface $userBuilder
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->userBuilder = $userBuilder;
    }

    /**
     * @Route(
     *     path="/users",
     *     name="user_list"
     * )
     *
     * @return Response
     *
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function listAction(): Response
    {
        return new Response(
            $this->twig->render(
                'user/list.html.twig',
                [
                    'users' => $this->entityManager->getRepository(User::class)
                                                   ->findAllUsers()
                ]
            )
        );
    }

    /**
     * @Route(
     *     path="/users/create",
     *     name="user_create"
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->create(RegistrationType::class)
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationDTO $datas */
            $datas = $form->getData();

            $this->entityManager->persist(
                $this->userBuilder->build($datas)
                                  ->getUser()
            );
            $this->entityManager->flush();

            $this->flashBag->add(
                'success',
                "L'utilisateur a bien été ajouté."
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('user_list')
            );
        }

        return new Response(
            $this->twig->render(
                'user/create.html.twig',
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * @Route(
     *     path="/users/{id}/edit",
     *     name="user_edit"
     * )
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function editAction(
        int $id,
        Request $request
    ): Response {
        $user = $this->entityManager->getRepository(User::class)
                                    ->findUserById($id);

        if (is_null($user)) {
            $this->flashBag->add(
                'error',
                "L'utilisateur demandé n'existe pas."
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('user_list')
            );
        }

        $form = $this->formFactory->create(
            RegistrationType::class,
            new RegistrationDTO(
                $user->getUsername(),
                $user->getPassword(),
                $user->getRoles()[0],
                $user->getEmail()
            )
        )
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationDTO $datas */
            $datas = $form->getData();

            $this->userBuilder->build(
                $datas,
                $user
            );

            $this->entityManager->flush();

            $this->flashBag->add(
                'success',
                "L'utilisateur a bien été modifié"
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('user_list')
            );
        }

        return new Response(
            $this->twig->render(
                'user/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user
                ]
            )
        );
    }
}
