<?php

namespace TubeService\Service;

use TubeService\Domain\Entity\Line;

class LineService extends Service
{
    const LINE_ENTITY = 'Line';

    public function findAll(): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::LINE_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.display_order', 'ASC');
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResultFromDatabaseResult($result);
    }

    public function findByKey($key) {
        $qb = $this->getQueryBuilder(self::LINE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.url_key = :key')
            ->setParameters(['key' => $key]);
        $result = $qb->getQuery()->getSingleResult();
        return $this->getServiceResultFromDatabaseResult($result)->getDomainModel();
    }

    public function findAllWithStatus(): ServiceResultInterface
    {
        $tblStatus = 's';

        $qb = $this->getQueryBuilder(self::LINE_ENTITY);
        $qb->select(self::TBL, $tblStatus);
        $qb->leftJoin(self::TBL . '.latest_status', $tblStatus);
        $qb->orderBy(self::TBL . '.display_order', 'ASC');
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResultFromDatabaseResult($result);
    }
}
