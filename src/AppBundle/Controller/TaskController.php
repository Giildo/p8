<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\TaskDTO;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * @Route(
     *     path="/tasks",
     *     name="task_list"
     * )
     *
     * @return Response
     */
    public function listAction(): Response
    {
        return $this->render(
            'task/list.html.twig',
            [
                'tasks' => $this->getDoctrine()
                                ->getRepository(Task::class)
                                ->findAllTasks(),
            ]
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
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(TaskType::class)
                     ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            /** @var TaskDTO $datas */
            $datas = $form->getData();

            $user = $this->get('security.token_storage')
                         ->getToken()
                         ->getUser();

            $em->persist(
                $this->get('task_builder')
                     ->build(
                         $datas,
                         null,
                         $user
                     )
                     ->getTask()
            );
            $em->flush();

            $this->addFlash(
                'success',
                'La tâche a été bien été ajoutée.'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/create.html.twig',
            [
                'form' => $form->createView()
            ]
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
     */
    public function editAction(
        int $id,
        Request $request
    ): Response {
        $task = $this->getDoctrine()
                     ->getRepository(Task::class)
                     ->findOneTaskById($id);

        if (is_null($task)) {
            return new RedirectResponse('/tasks');
        }

        $form = $this->createForm(
            TaskType::class,
            new TaskDTO(
                $task->getTitle(),
                $task->getContent()
            )
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TaskDTO $datas */
            $datas = $form->getData();

            $this->get('task_builder')
                 ->build(
                     $datas,
                     $task
                 );

            $this->getDoctrine()
                 ->getManager()
                 ->flush();

            $this->addFlash(
                'success',
                'La tâche a bien été modifiée.'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
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
        $task = $this->getDoctrine()
                     ->getRepository(Task::class)
                     ->findOneTaskById($id);
        $task->toggle();

        $this->getDoctrine()
             ->getManager()
             ->flush();

        $message = 'non terminée';

        if ($task->isDone()) {
            $message = 'terminée';
        }

        $this->addFlash(
            'success',
            sprintf(
                'La tâche "%1$s" a bien été marquée comme %2$s.',
                $task->getTitle(),
                $message
            )
        );

        return $this->redirectToRoute('task_list');
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
        $task = $this->getDoctrine()
                     ->getRepository(Task::class)
                     ->findOneTaskById($id);

        $em = $this->getDoctrine()
                   ->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash(
            'success',
            'La tâche a bien été supprimée.'
        );

        return $this->redirectToRoute('task_list');
    }
}
