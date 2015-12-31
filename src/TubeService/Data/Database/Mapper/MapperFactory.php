<?php

namespace TubeService\Data\Database\Mapper;

use Symfony\Component\Config\Definition\Exception\Exception;
use TubeService\Data\Database\Entity\Line;
use TubeService\Data\Database\Entity\Notification;
use TubeService\Data\Database\Entity\Status;
use TubeService\Data\Database\Entity\Subscription;
use TubeService\Domain\Entity\Entity;

class MapperFactory
{

    public function __construct()
    {
    }

    public function getMapper($item): Mapper
    {
        // decide which mapper is needed based on the incoming data
        if ($item instanceof Line) {
            return $this->createLineMapper();
        }
        if ($item instanceof Status) {
            return $this->createStatusMapper();
        }
        if ($item instanceof Subscription) {
            return $this->createSubscriptionMapper();
        }
        if ($item instanceof Notification) {
            return $this->createNotificationMapper();
        }
        throw new Exception('Unrecognisable Data. Cannot map');
    }

    public function getDomainModel($item): Entity
    {
        $mapper = $this->getMapper($item);
        return $mapper->getDomainModel($item);
    }

    public function createLineMapper(): LineMapper
    {
        $mapper = new LineMapper($this);
        return $mapper;
    }

    public function createStatusMapper(): StatusMapper
    {
        $mapper = new StatusMapper($this);
        return $mapper;
    }

    public function createSubscriptionMapper(): SubscriptionMapper
    {
        $mapper = new SubscriptionMapper($this);
        return $mapper;
    }

    public function createNotificationMapper(): NotificationMapper
    {
        $mapper = new NotificationMapper($this);
        return $mapper;
    }
}
