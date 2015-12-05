<?php

namespace AppBundle\Service;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\ISIN;

class SecuritiesService extends Service
{
    const SECURITY_ENTITY = 'Security';
    const CURRENCY_ENTITY = 'Currency';

    public function findAndCountAll(
        int $limit,
        int $page = 1
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAll($limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(tbl.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(
        int $limit,
        int $page = 1
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('tbl', 'c', 'co', 'fsa');
        $qb->join('tbl.currency', 'c');
        $qb->join('tbl.company', 'co');
        $qb->join('tbl.fsa04748', 'fsa');
        $qb->orderBy('tbl.isin', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function searchAndCount(
        string $query,
        int $limit,
        int $page = 1
    ): ServiceResultInterface {
        $count = $this->countSearch($query);

        if ($count === 0) {
            return new ServiceResultEmpty();
        }

        // find them
        $result = $this->search($query, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countSearch(string $query): int
    {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(tbl.id)');
        $qb->andWhere('tbl.isin LIKE ?0');
        $qb->setParameters(['%' . $query . '%']);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function search(
        string $query,
        int $limit,
        int $page = 1
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('tbl', 'c');
        $qb->join('tbl.currency', 'c');

        $qb->andWhere('tbl.isin LIKE :query');
        $qb->setParameters(['query' => '%' . $query . '%']);

        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByIsin(ISIN $isin): ServiceResultInterface
    {
        $entity = $this->getEntity(self::SECURITY_ENTITY);

        $result = $entity->findBy(
            ['isin' => $isin]
        );

        return $this->getServiceResult($result);
    }
}
