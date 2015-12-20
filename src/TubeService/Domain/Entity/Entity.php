<?php

namespace TubeService\Domain\Entity;

use JsonSerializable;
use TubeService\Domain\ValueObject\ID;
use DateTime;

/**
 * Class Entity
 * For those which the base object inherit
 */
abstract class Entity implements JsonSerializable
{
    /**
     * @param ID $id
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @var string
     */
    protected $id;

    public function getId(): ID
    {
        return $this->id;
    }

    /**
     * @var \DateTime
     */
    protected $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'createdAt' => $this->getCreatedAt()->format('c'),
            'updatedAt' => $this->getUpdatedAt()->format('c')
        ];
    }
}
