<?php

use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DoctrineContext extends MinkContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserInterface
     */
    private $user1;

    /**
     * @var UserInterface
     */
    private $user2;

    /**
     * DoctrineContext constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @BeforeScenario
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function initDatabase()
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema(
            $this->entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );
        $schemaTool->createSchema(
            $this->entityManager->getMetadataFactory()
                                ->getAllMetadata()
        );
    }

    /**
     * @Given I am logged with username :username and with password :password
     */
    public function iAmLoggedWithUsernameAndWithPassword(
        $username,
        $password
    ) {
        $this->visit('/login');
        $this->fillField(
            'connection_username',
            $username
        );
        $this->fillField(
            'connection_password',
            $password
        );
        $this->pressButton('Valider');
    }

    /**
     * @Given I load a specific user
     */
    public function iLoadASpecificUser()
    {
        $this->user1 = new User(
            'JohnDoe',
            'ROLE_USER',
            'john@doe.com'
        );

        $this->user2 = new User(
            'JaneDoe',
            'ROLE_ADMIN',
            'jane@doe.com'
        );

        $this->user1->setPassword(
            $this->passwordEncoder->encodePassword(
                $this->user1,
                '12345678'
            )
        );
        $this->user2->setPassword(
            $this->passwordEncoder->encodePassword(
                $this->user2,
                '12345678'
            )
        );

        $this->entityManager->persist($this->user1);
        $this->entityManager->persist($this->user2);

        $this->entityManager->flush();
    }

    /**
     * @Given /^I load one specific user$/
     */
    public function iLoadOneSpecificUser()
    {
        $user1 = new User(
            'JaneDoe',
            'ROLE_ADMIN',
            'jane@doe.com'
        );

        $user1->setPassword(
            $this->passwordEncoder->encodePassword(
                $user1,
                '12345678'
            )
        );

        $this->entityManager->persist($user1);

        $this->entityManager->flush();
    }

    /**
     * @Given I have saved tasks
     */
    public function iHaveSavedTasks()
    {
        $this->iLoadASpecificUser();

        $task1 = new Task(
            'Titre de la première tâche',
            'Contenu de la première tâche',
            $this->user1
        );

        $task2 = new Task(
            'Titre de la seconde tâche',
            'Contenu de la seconde tâche',
            $this->user1
        );

        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);

        $this->entityManager->flush();
    }

    /**
     * @Given /^I have saved one task$/
     */
    public function iHaveSavedOneTask()
    {
        $this->iLoadASpecificUser();

        $task1 = new Task(
            'Titre de la première tâche',
            'Contenu de la première tâche',
            $this->user1
        );

        $this->entityManager->persist($task1);

        $this->entityManager->flush();
    }
}
