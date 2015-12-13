<?php

namespace TubeService\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use TubeService\Data\Database\Mapper\MapperFactory;

abstract class Service
{
    const TBL = 'tbl';

    protected $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function getEntity(string $name): EntityRepository
    {
        return $this->entityManager
            ->getRepository('TubeService:' . $name);
    }

    public function getQueryBuilder(string $name) {
        $entity = $this->getEntity($name);
        return $entity->createQueryBuilder(self::TBL);
    }

    public function getServiceResultFromDatabaseResult($result)
    {
        $data = $this->getDomainModelsFromDatabaseResult($result);

        if ($data) {
            return new ServiceResult($data);
        }
        return new ServiceResultEmpty();
    }

    private function getDomainModelsFromDatabaseResult($items)
    {
        if (!$items) {
            return null;
        }
        if (!is_array($items)) {
            $items = [$items];
        }

        $mapperFactory = new MapperFactory();

        $domainModels = array();
        foreach ($items as $item) {
            $mapper = $mapperFactory->getMapper($item);
            $domainModels[] = $mapper->getDomainModel($item);
        }
        return $domainModels;
    }

    public function calculateOffset(
        int $limit,
        int $page
    ): int {
        return ($limit * ($page - 1));
    }
}
