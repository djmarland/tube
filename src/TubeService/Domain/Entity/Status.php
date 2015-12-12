<?php

namespace TubeService\Domain\Entity;

use TubeService\Domain\ValueObject\ID;
use DateTime;

/**
 * Class Entity
 * For those which the base object inherit
 */
class Status extends Entity
{
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        bool $isDisrupted,
        string $shortTitle,
        string $title,
        string $description
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->isDisrupted = $isDisrupted;
        $this->shortTitle = $shortTitle;
        $this->title = $title;
        $this->description = $description;
    }

    private $isDisrupted;

    public function isDisrupted()
    {
        return $this->isDisrupted;
    }

    private $shortTitle;

    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    private $title;

    public function getTitle()
    {
        return $this->title;
    }

    private $description;

    public function getDescription()
    {
        return $this->description;
    }
}