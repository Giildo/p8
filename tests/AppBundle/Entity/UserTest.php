<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @var UserInterface
     */
    private $user;

    public function setUp()
    {
        $this->user = new User(
            'JohnDoe',
            'ROLE_ADMIN',
            'john@doe.fr'
        );
    }

    public function testEraseCredentials()
    {
        self::assertNull($this->user->eraseCredentials());
    }

    public function testGetSalt()
    {
        self::assertEquals('', $this->user->getSalt());
    }
}
