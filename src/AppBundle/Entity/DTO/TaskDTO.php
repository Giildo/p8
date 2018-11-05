<?php

namespace AppBundle\Entity\DTO;

use AppBundle\Entity\DTO\Interfaces\TaskDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TaskDTO implements TaskDTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le titre de la tâche doit être renseigné.")
     * @Assert\Length(
     *     min="3",
     *     minMessage="Le titre de la tâche doit comporter au moins {{ limit }} caractères.",
     *     max="255",
     *     maxMessage="Le titre de la tâche ne doit pas comporter plus de {{ limit }} caractère."
     * )
     */
    public $title;

    /**
     * @var string
     *
     * @Assert\NotNull(message="Le contenu de la tâche doit être renseigné.")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Le contenu de la tâche doit comporter au moins {{ limit }} caractères."
     * )
     */
    public $content;

    /**
     * TaskDTO constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(
        string $title,
        string $content
    ) {
        $this->title = $title;
        $this->content = $content;
    }
}
