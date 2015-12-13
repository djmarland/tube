<?php

namespace TubeService\Data\Database\Mapper;

use TubeService\Domain\Entity\Status;
use TubeService\Domain\ValueObject\ID;

class StatusMapper extends Mapper
{
    public function getDomainModel(\TubeService\Data\Database\Entity\Status $item): Status
    {
        $id = new ID($item->getID());

        $descriptions = null;
        $description = $item->getDescription();
        if ($description) {
            $descriptions = explode('|', $description);
        }

        $line = new Status(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getIsDisrupted(),
            $item->getShortTitle(),
            $item->getTitle(),
            $descriptions
        );
        return $line;
    }


}
