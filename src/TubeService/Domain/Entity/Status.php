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
        $descriptions = null
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->isDisrupted = $isDisrupted;
        $this->shortTitle = $shortTitle;
        $this->title = $title;
        if (!is_null($descriptions) && !is_array($descriptions)) {
            $descriptions = [$descriptions];
        }
        $this->descriptions = $descriptions;
    }

    private $isDisrupted;

    public function isDisrupted(): bool
    {
        return $this->isDisrupted;
    }

    private $shortTitle;

    public function getShortTitle(): string
    {
        return $this->shortTitle;
    }

    private $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    private $descriptions;

    public function getDescriptions()
    {
        return $this->descriptions;
    }

    public function getUpdatedAtFormatted()
    {
        return $this->getUpdatedAt()->format('D j M Y H:i');
    }

    public function getUpdatedAtISO()
    {
        return $this->getUpdatedAt()->format('c');
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['isDisrupted'] = $this->isDisrupted();
        $data['title'] = $this->getTitle();
        $data['shortTitle'] = $this->getShortTitle();
        $data['descriptions'] = $this->getDescriptions();
        $data['updatedAtFormatted'] = $this->getUpdatedAtFormatted();
        return $data;
    }
}