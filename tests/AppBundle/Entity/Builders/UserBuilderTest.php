<?php

namespace Tests\AppBundle\Entity\Builders;

use AppBundle\Entity\Builders\Interfaces\UserBuilderInterface;
use AppBundle\Entity\Builders\UserBuilder;
use AppBundle\Entity\DTO\Interfaces\RegistrationDTOInterface;
use AppBundle\Entity\DTO\RegistrationDTO;
use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserBuilderTest extends KernelTestCase
{
    /**
     * @var UserBuilderInterface
     */
    private $userBuilder;

    /**
     * @var RegistrationDTOInterface
     */
    private $registrationDTO;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function setUp()
    {
        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $passwordEncoder->method('encodePassword')
                        ->willReturn('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $this->userBuilder = new UserBuilder($passwordEncoder);

        $this->registrationDTO = new RegistrationDTO(
            'JohnDoe',
            '12345678',
            'ROLE_ADMIN',
            'john@doe.fr'
        );

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
                                      ->get('doctrine.orm.entity_manager');


        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()
                                                    ->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()
                                                      ->getAllMetadata());

        $oldUser = new User(
            'JaneDoe',
            'ROLE_USER',
            'jane@doe.fr'
        );
        $oldUser->setPassword('$2y$10$LTxgYE871HV.OHE//yZF/e6VfVPA95WCDTW3y9SM4zKjm78VvPIVa');

        $this->entityManager->persist($oldUser);
        $this->entityManager->flush();
    }

    public function testCreationOfUser()
    {
        $user = $this->userBuilder->build($this->registrationDTO)
                                  ->getUser();

        self::assertInstanceOf(UserInterface::class, $user);
        self::assertEquals('JohnDoe', $user->getUsername());
        self::assertEquals('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu', $user->getPassword());
        self::assertEquals(['ROLE_ADMIN'], $user->getRoles());
        self::assertEquals('john@doe.fr', $user->getEmail());
    }

    public function testEditionOfUser()
    {
        $oldUser = $this->entityManager->getRepository(User::class)
                                       ->findUserByUsername('JaneDoe');
        $this->userBuilder->build($this->registrationDTO, $oldUser);

        self::assertInstanceOf(UserInterface::class, $oldUser);
        self::assertEquals('JohnDoe', $oldUser->getUsername());
        self::assertEquals('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu', $oldUser->getPassword());
        self::assertEquals(['ROLE_ADMIN'], $oldUser->getRoles());
        self::assertEquals('john@doe.fr', $oldUser->getEmail());
    }
}
