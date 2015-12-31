<?php

namespace TubeService\Data\Database\Mapper;

use TubeService\Domain\Entity\Line;
use TubeService\Domain\ValueObject\ID;

class LineMapper extends Mapper
{
    public function getDomainModel(\TubeService\Data\Database\Entity\Line $item): Line
    {
        $id = new ID($item->getID());

        $latestStatus = null;

        if ($item->hasLatestStatus()) { // don't lazy load if it wasn't included
            $latestStatus = $this->mapperFactory->getDomainModel($item->getLatestStatus());
        }


        $line = new Line(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getURLKey(),
            $item->getTFLKey(),
            $item->getName(),
            $item->getShortName(),
            $item->getDisplayOrder(),
            $latestStatus
        );


        return $line;
    }


}
