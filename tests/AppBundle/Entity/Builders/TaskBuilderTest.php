<?php

namespace Tests\AppBundle\Entity\Builders;

use AppBundle\Entity\Builders\TaskBuilder;
use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use AppBundle\Entity\DTO\TaskDTO;
use AppBundle\Entity\Interfaces\TaskInterface;
use AppBundle\Entity\Task;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskBuilderTest extends KernelTestCase
{
    /**
     * @var TaskBuilder
     */
    private $taskBuilder;

    /**
     * @var TaskDTOInterface
     */
    private $taskDTO;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DateTime
     */
    private $date;

    public function setUp()
    {
        $this->taskBuilder = new TaskBuilder();

        $this->taskDTO = new TaskDTO(
            'Titre',
            'Contenu'
        );

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
                                      ->get('doctrine.orm.entity_manager');


        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()
                                                    ->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()
                                                      ->getAllMetadata());

        $this->date = new DateTime();
        $oldTask = new Task(
            'Ancien titre',
            'Ancien contenu'
        );

        $this->entityManager->persist($oldTask);
        $this->entityManager->flush();
    }

    public function testCreationOfTask()
    {
        $task = $this->taskBuilder->build($this->taskDTO)
                                  ->getTask();

        self::assertInstanceOf(TaskInterface::class, $task);
        self::assertEquals('Titre', $task->getTitle());
        self::assertEquals('Contenu', $task->getContent());
        self::assertEquals(
            $this->date->format('Y-m-d H:i'),
            $task->getCreatedAt()->format('Y-m-d H:i')
        );
    }

    public function testEditionOfTask()
    {
        $oldTask = $this->entityManager->getRepository(Task::class)
                                       ->findOneTaskById(1);

        $this->taskBuilder->build($this->taskDTO, $oldTask);

        self::assertInstanceOf(TaskInterface::class, $oldTask);
        self::assertEquals('Titre', $oldTask->getTitle());
        self::assertEquals('Contenu', $oldTask->getContent());
        self::assertEquals(
            $this->date->format('Y-m-d H:i'),
            $oldTask->getCreatedAt()->format('Y-m-d H:i')
        );
    }
}
