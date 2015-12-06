<?php

namespace TubeService\Data\TFL\Mapper;

use TubeService\Domain\Entity\Line;
use TubeService\Domain\ValueObject\ID;

class LineMapper extends Mapper
{
    public function getDomainModel($item): Line
    {
        $id = new ID(0);
        $statuses = [];
        foreach ($item->lineStatuses as $status) {
            $statuses[] = $status->statusSeverityDescription;
        }
        $statuses = array_unique($statuses);

        $line = new Line(
            $id,
            new \DateTime(),
            new \DateTime(),
            $item->id,
            $item->name,
            $statuses
        );
        return $line;
    }


}
