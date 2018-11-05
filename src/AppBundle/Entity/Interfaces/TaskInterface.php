<?php

namespace AppBundle\Entity\Interfaces;

use DateTime;

interface TaskInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return bool
     */
    public function isDone(): bool;

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content): void;

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void;

    /**
     * @param DateTime $createdAt
     *
     * @return void
     */
    public function setCreatedAt(DateTime $createdAt): void;

    /**
     * @return void
     */
    public function toggle(): void;
}