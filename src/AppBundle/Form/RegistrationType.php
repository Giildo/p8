<?php

namespace AppBundle\Form;

use AppBundle\Entity\DTO\Interfaces\RegistrationDTOInterface;
use AppBundle\Entity\DTO\RegistrationDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'required' => false,
                    'label'    => 'Nom d\'utilisateur',
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'required'        => false,
                    'first_options'   => [
                        'label'    => 'Mot de passe',
                    ],
                    'second_options'  => [
                        'label'    => 'Tapez le mot de passe à nouveau',
                    ],
                ]
            )
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices'     => [
                        'Utilisateur'    => 'ROLE_USER',
                        'Administrateur' => 'ROLE_ADMIN',
                    ],
                    'required'    => false,
                    'label'       => 'Rôle de l\'utilisateur',
                    'placeholder' => false,
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'required' => false,
                    'label'    => 'Adresse email',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RegistrationDTOInterface::class,
                'empty_data' => function (FormInterface $form) {
                    return new RegistrationDTO(
                        $form->get('username')->getData(),
                        $form->get('password')->getData(),
                        $form->get('roles')->getData(),
                        $form->get('email')->getData()
                    );
                },
            ]
        );
    }
}
