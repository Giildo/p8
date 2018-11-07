<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\DTO\Interfaces\ConnectionDTOInterface;
use AppBundle\Form\ConnectionType;
use Symfony\Component\Form\Test\TypeTestCase;

class ConnectionTypeTest extends TypeTestCase
{
    public function testReturnOfTheFormType()
    {
        $form = $this->factory->create(ConnectionType::class);

        $datas = [
            'username' => 'JohnDoe',
            'password' => '12345678',
        ];

        $form->submit($datas);

        $dto = $form->getData();

        self::assertInstanceOf(ConnectionDTOInterface::class, $dto);
        self::assertEquals('JohnDoe', $dto->username);
        self::assertEquals('12345678', $dto->password);
    }
}
