<?php

namespace TubeService\Service;

use Exception;
use TubeService\Data\Database\Entity\Subscription;
use TubeService\Domain\Entity\Line;

class SubscriptionService extends Service
{
    const SUBSCRIPTION_ENTITY = 'Subscription';

    public function findAllForEndpoint($endpoint): ServiceResultInterface {
        $tblLine = 'l';

        $qb = $this->getQueryBuilder(self::SUBSCRIPTION_ENTITY);
        $qb->select(self::TBL, $tblLine)
            ->where(self::TBL . '.endpoint = :endpoint', self::TBL . '.is_active = 1')
            ->setParameters(['endpoint' => $endpoint])
            ->leftJoin(self::TBL . '.line', $tblLine);
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResultFromDatabaseResult($result);
    }

    public function findAllForLineAndTime(
        Line $line,
        int $day,
        int $hour
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SUBSCRIPTION_ENTITY);
        $qb->select(self::TBL)
            ->where(
                'IDENTITY(' . self::TBL . '.line) = :line',
                self::TBL . '.is_active = 1',
                self::TBL . '.day = :day',
                self::TBL . '.start_hour <= :hour',
                self::TBL . '.end_hour >= :hour'
            )
            ->setParameters([
                'line' => $line->getId()->getId(),
                'day' => $day,
                'hour' => $hour
            ]);
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResultFromDatabaseResult($result);

        // get all subscriptions where
        // - they are for this line
        // - they are active
        // - they are today
        // - they have a start hour before or equal to now AND
        // - they have an end hour after or equal to now
    }

    public function findAllForLineAndHour(
        Line $line,
        int $day,
        int $hour
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SUBSCRIPTION_ENTITY);
        $qb->select(self::TBL)
            ->where(
                'IDENTITY(' . self::TBL . '.line) = :line',
                self::TBL . '.is_active = 1',
                self::TBL . '.day = :day',
                self::TBL . '.start_hour = :hour'
            )
            ->setParameters([
                'line' => $line->getId()->getId(),
                'day' => $day,
                'hour' => $hour
            ]);
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResultFromDatabaseResult($result);

        // get all subscriptions where
        // - they are for this line
        // - they are active
        // - they are today
        // - they have a start hour before or equal to now AND
        // - they have an end hour after or equal to now
    }

    public function setForLine(
        Line $line,
        $endpoint,
        $times,
        $key = null
    ) {
        $qb = $this->getQueryBuilder(self::SUBSCRIPTION_ENTITY);

        $lineEntity = $this->entityManager
            ->find('TubeService:Line', $line->getId());

        // start a transaction
        $this->entityManager->getConnection()->beginTransaction();
        try {
            // set all previous subscriptions for this line and endpoint to inactive
            $q = $qb->update()
                ->set(self::TBL . '.is_active', '0')
                ->where(self::TBL . '.line = ?1', self::TBL . '.endpoint = ?2')
                ->setParameter(1, $line->getId()->getId())
                ->setParameter(2, $endpoint)
                ->getQuery();
            $q->execute();

            // create the domain entities for each time block
            foreach ($times as $day => $groups) {
                foreach ($groups as $group) {
                    $subscription = new Subscription();
                    $subscription->setLine($lineEntity);
                    $subscription->setDay($day);
                    $subscription->setEndpoint($endpoint);
                    $subscription->setPublicKey($key);
                    $subscription->setStartHour($group->start);
                    $subscription->setEndHour($group->end);
                    $this->entityManager->persist($subscription);
                }
            }

            // commit the transaction
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function removeForEndpoint(
        string $endpoint
    ) {
        $qb = $this->getQueryBuilder(self::SUBSCRIPTION_ENTITY);

        // start a transaction
        $this->entityManager->getConnection()->beginTransaction();
        try {
            // set all previous subscriptions for this line and endpoint to inactive
            $q = $qb->update()
                ->set(self::TBL . '.is_active', '0')
                ->where(self::TBL . '.endpoint = :endpoint')
                ->setParameter('endpoint', $endpoint)
                ->getQuery();
            $q->execute();

            // commit the transaction
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }
}
