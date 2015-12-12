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
        string $URLKey,
        string $TFLKey,
        string $name,
        string $shortName,
        int $displayOrder,
        Status $latestStatus = null
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->shortName = $shortName;
        $this->URLKey = $URLKey;
        $this->TFLKey = $TFLKey;
        $this->displayOrder = $displayOrder;
        $this->latestStatus = $latestStatus;
    }

    private $name;

    public function getName()
    {
        return $this->name;
    }

    private $shortName;

    public function getShortName()
    {
        return $this->shortName;
    }

    private $URLKey;

    public function getURLKey()
    {
        return $this->URLKey;
    }

    private $TFLKey;

    public function getTFLKey()
    {
        return $this->TFLKey;
    }

    private $displayOrder;

    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    private $latestStatus;

    public function getLatestStatus()
    {
        return $this->latestStatus;
    }

    public function isDisrupted()
    {
        if ($this->latestStatus) {
            return $this->latestStatus->isDisrupted();
        }
        return false;
    }

    public function getStatusSummary()
    {
        if ($this->latestStatus) {
            return $this->latestStatus->getShortTitle();
        }
        return 'No Information';
    }
}