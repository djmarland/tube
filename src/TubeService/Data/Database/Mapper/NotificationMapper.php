<?php

namespace TubeService\Data\Database\Mapper;

use TubeService\Domain\Entity\Notification;
use TubeService\Domain\ValueObject\ID;

class NotificationMapper extends Mapper
{
    public function getDomainModel(\TubeService\Data\Database\Entity\Notification $item): Notification
    {
        $id = new ID($item->getID());


        $image = new Notification(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getEndpoint(),
            $item->getTitle(),
            $item->getDescription(),
            $item->getUrl(),
            $item->getImage()
        );

        return $image;
    }


}
