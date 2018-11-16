<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use AppBundle\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testReturnOfTheFormType()
    {
        $form = $this->factory->create(TaskType::class);

        $datas = [
            'title'   => 'Titre',
            'content' => 'Contenu',
        ];

        $form->submit($datas);

        $dto = $form->getData();

        self::assertInstanceOf(TaskDTOInterface::class, $dto);
        self::assertEquals('Titre', $dto->title);
        self::assertEquals('Contenu', $dto->content);
    }
}
