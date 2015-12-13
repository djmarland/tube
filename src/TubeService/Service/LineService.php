<?php

namespace TubeService\Service;

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

    public function findAllWithStatus()
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
