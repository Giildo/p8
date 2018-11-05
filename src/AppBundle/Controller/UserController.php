<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Builders\UserBuilder;
use AppBundle\Entity\DTO\RegistrationDTO;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route(
     *     path="/users",
     *     name="user_list"
     * )
     *
     * @return Response
     */
    public function listAction(): Response
    {
        return $this->render(
            'user/list.html.twig',
            [
                'users' => $this->getDoctrine()
                                ->getRepository('AppBundle:User')
                                ->findAll()
            ]
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
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(RegistrationType::class)
                     ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();

            /** @var RegistrationDTO $datas */
            $datas = $form->getData();

            $em->persist(
                $this->container->get('user_builder')
                                ->build($datas)
                                ->getUser()
            );
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
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
     */
    public function editAction(
        int $id,
        Request $request
    ): Response {
        $user = $this->getDoctrine()
                     ->getRepository(User::class)
                     ->findUserById($id);

        $form = $this->createForm(
            RegistrationType::class,
            new RegistrationDTO(
                $user->getUsername(),
                $user->getPassword(),
                $user->getRoles()[0],
                $user->getEmail()
            )
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationDTO $datas */
            $datas = $form->getData();

            $this->get('user_builder')
                 ->build($datas, $user);

            $this->getDoctrine()
                 ->getManager()
                 ->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]
        );
    }
}
