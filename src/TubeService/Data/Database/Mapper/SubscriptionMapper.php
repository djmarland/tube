<?php

namespace TubeService\Data\Database\Mapper;

use TubeService\Domain\Entity\Subscription;
use TubeService\Domain\ValueObject\ID;

class SubscriptionMapper extends Mapper
{
    public function getDomainModel(\TubeService\Data\Database\Entity\Subscription $item): Subscription
    {
        $id = new ID($item->getID());

        $line = null;

        $lineEntity = $item->getLine();
        if ($lineEntity) {
            $line = $this->mapperFactory->getDomainModel($lineEntity);
        }

        $subscription = new Subscription(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getEndpoint(),
            $item->getDay(),
            $item->getStartHour(),
            $item->getEndHour(),
            $item->getIsActive(),
            $line
        );

        return $subscription;
    }


}
