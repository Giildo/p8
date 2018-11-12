<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Builders\Interfaces\TaskBuilderInterface;
use AppBundle\Entity\DTO\TaskDTO;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class TaskController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var TaskBuilderInterface
     */
    private $taskBuilder;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * TaskController constructor.
     * @param Environment $twig
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param TaskBuilderInterface $taskBuilder
     * @param UrlGeneratorInterface $urlGenerator
     * @param FlashBagInterface $flashBag
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        TaskBuilderInterface $taskBuilder,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->taskBuilder = $taskBuilder;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route(
     *     path="/tasks",
     *     name="task_list"
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
                'task/list.html.twig',
                [
                    'tasks' => $this->entityManager->getRepository(Task::class)
                                                   ->findAllTasks(),
                ]
            )
        );
    }

    /**
     * @Route(
     *     path="/tasks/create",
     *     name="task_create"
     * )
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
        $form = $this->formFactory->create(TaskType::class)
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TaskDTO $datas */
            $datas = $form->getData();

            $user = $this->tokenStorage->getToken()
                                       ->getUser();

            $this->entityManager->persist(
                $this->taskBuilder->build(
                    $datas,
                    null,
                    $user
                )
                                  ->getTask()
            );
            $this->entityManager->flush();

            $this->flashBag->add(
                'success',
                'La tâche a été bien été ajoutée.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        return new Response(
            $this->twig->render(
                'task/create.html.twig',
                [
                    'form' => $form->createView()
                ]
            )
        );
    }

    /**
     * @Route(
     *     path="/tasks/{id}/edit",
     *     name="task_edit"
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
        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById($id);

        if (is_null($task)) {
            $this->flashBag->add(
                'error',
                'La tâche demandée n\'existe pas.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        $form = $this->formFactory->create(
            TaskType::class,
            new TaskDTO(
                $task->getTitle(),
                $task->getContent()
            )
        )
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TaskDTO $datas */
            $datas = $form->getData();

            $this->taskBuilder->build(
                $datas,
                $task
            );

            $this->entityManager->flush();

            $this->flashBag->add(
                'success',
                'La tâche a bien été modifiée.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        return new Response(
            $this->twig->render(
                'task/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'task' => $task,
                ]
            )
        );
    }

    /**
     * @Route(
     *     path="/tasks/{id}/toggle",
     *     name="task_toggle"
     * )
     *
     * @param int $id
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function toggleTaskAction(int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById($id);

        if (is_null($task)) {
            $this->flashBag->add(
                'error',
                'La tâche demandée n\'existe pas.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        $task->toggle();

        $this->entityManager->flush();

        $message = 'non terminée';

        if ($task->isDone()) {
            $message = 'terminée';
        }

        $this->flashBag->add(
            'success',
            sprintf(
                'La tâche "%1$s" a bien été marquée comme %2$s.',
                $task->getTitle(),
                $message
            )
        );

        return new RedirectResponse(
            $this->urlGenerator->generate('task_list')
        );
    }

    /**
     * @Route(
     *     path="/tasks/{id}/delete",
     *     name="task_delete"
     * )
     *
     * @param int $id
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function deleteTaskAction(int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)
                                    ->findOneTaskById($id);

        if (is_null($task)) {
            $this->flashBag->add(
                'error',
                'La tâche demandée n\'existe pas.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        $userConnected = $this->tokenStorage->getToken()
                                            ->getUser();

        if (
            $task->getUser() === $userConnected ||
            (
                $task->getUser() === null &&
                $this->authorizationChecker->isGranted('ROLE_ADMIN')
            )
        ) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            $this->flashBag->add(
                'success',
                'La tâche a été supprimée.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        $this->flashBag->add(
            'error',
            'Vous n\'avez pas les droits pour supprimer cette tâche.'
        );

        return new RedirectResponse(
            $this->urlGenerator->generate('task_list')
        );
    }
}
