<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Interfaces\TaskInterface;
use AppBundle\Entity\Interfaces\UserInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 * @ORM\Table("p8_task")
 */
class Task implements TaskInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vous devez saisir un titre.")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Vous devez saisir du contenu.")
     */
    private $content;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $done;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    public function __construct(
        string $title,
        string $content,
        ?UserInterface $user = null
    ) {
        $this->createdAt = new Datetime();
        $this->done = false;
        $this->title = $title;
        $this->content = $content;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return void
     */
    public function toggle(): void
    {
        $this->done = !$this->done;
    }
}
