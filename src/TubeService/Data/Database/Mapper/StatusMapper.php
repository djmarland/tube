<?php

namespace TubeService\Data\Database\Mapper;

use TubeService\Domain\Entity\Status;
use TubeService\Domain\ValueObject\ID;

class StatusMapper extends Mapper
{
    public function getDomainModel(\TubeService\Data\Database\Entity\Status $item): Status
    {
        $id = new ID($item->getID());

        $line = new Status(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getIsDisrupted(),
            $item->getShortTitle(),
            $item->getTitle(),
            $item->getDescription()
        );
        return $line;
    }


}
