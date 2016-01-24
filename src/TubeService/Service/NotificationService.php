<?php

namespace TubeService\Service;

use Exception;
use TubeService\Data\Database\Entity\Notification;

class NotificationService extends Service
{
    const NOTIFICATION_ENTITY = 'Notification';

    public function findForEndpoint($endpoint): ServiceResultInterface
    {
        $qb = $this->getQueryBuilder(self::NOTIFICATION_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.endpoint = :endpoint', self::TBL . '.processed = 0')
            ->setParameters(['endpoint' => $endpoint])
            ->orderBy(self::TBL . '.created_at', 'DESC')
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResultFromDatabaseResult($result);
    }

    public function createNew(
        string $endpoint,
        string $title,
        string $description,
        string $url,
        string $image
    ) {
        $notification = new Notification();
        $notification->setEndpoint($endpoint);
        $notification->setTitle($title);
        $notification->setDescription($description);
        $notification->setUrl($url);
        $notification->setImage($image);
        $this->entityManager->persist($notification);

        $this->entityManager->flush();
        return $notification;
    }
}
