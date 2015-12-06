<?php

namespace TubeService\Domain\Entity;

use TubeService\Domain\ValueObject\ID;
use DateTime;

/**
 * Class Entity
 * For those which the base object inherit
 */
class Line extends Entity
{
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        $key,
        $name,
        $statuses
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->key = $key;
        $this->statuses = $statuses;
    }

    private $name;

    public function getName()
    {
        return $this->name;
    }

    private $key;

    public function getKey()
    {
        return $this->key;
    }

    private $statuses;

    public function getStatuses()
    {
        return $this->statuses;
    }

    public function getStatus()
    {
        return implode(', ', $this->getStatuses());
    }
}