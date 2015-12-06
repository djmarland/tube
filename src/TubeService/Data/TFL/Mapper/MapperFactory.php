<?php

namespace TubeService\Data\TFL\Mapper;

use TubeService\Domain\Entity\Entity;

/**
 * Factory to create mappers as needed
 */
class MapperFactory
{

    public function __construct()
    {
    }

    public function getMapper($item): Mapper
    {
        // decide which mapper is needed based on the incoming data
        if ($item ) {
            return $this->createLineMapper();
        }
    }

    public function getDomainModel($item): Entity
    {
        $mapper = $this->getMapper($item);
        return $mapper->getDomainModel($item);
    }

    public function createLineMapper(): LineMapper
    {
        $settingsMapper = new LineMapper($this);
        return $settingsMapper;
    }
}
