<?php

namespace AppBundle\Form;

use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use AppBundle\Entity\DTO\TaskDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'required' => false,
                    'label'    => 'Titre',
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'required' => false,
                    'label'    => 'Contenu',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TaskDTOInterface::class,
                'empty_data' => function (FormInterface $form) {
                    return new TaskDTO(
                        $form->get('title')->getData(),
                        $form->get('content')->getData()
                    );
                },
            ]
        );
    }
}
