<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\DTO\Interfaces\RegistrationDTOInterface;
use AppBundle\Form\RegistrationType;
use Symfony\Component\Form\Test\TypeTestCase;

class RegistrationTypeTest extends TypeTestCase
{
    public function testReturnOfTheFormType()
    {
        $form = $this->factory->create(RegistrationType::class);

        $datas = [
            'username' => 'JohnDoe',
            'password' => [
                'first'  => '12345678',
                'second' => '12345678'
            ],
            'roles'    => 'ROLE_ADMIN',
            'email'    => 'john@doe.fr',
        ];

        $form->submit($datas);

        $dto = $form->getData();

        self::assertInstanceOf(RegistrationDTOInterface::class, $dto);
        self::assertEquals('JohnDoe', $dto->username);
        self::assertEquals('12345678', $dto->password);
        self::assertEquals('ROLE_ADMIN', $dto->roles);
        self::assertEquals('john@doe.fr', $dto->email);
    }
}
