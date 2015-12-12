<?php

namespace TubeService\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use GuzzleHttp\ClientInterface;
use TubeService\Data\Database\Mapper\MapperFactory;
use TubeService\Data\TFL\Query\StatusQuery;

abstract class Service
{
    const TBL = 'tbl';

    protected $httpClient;

    protected $entityManager;

    protected $mapperFactory;

    public function __construct(
        ClientInterface $httpClient,
        EntityManager $entityManager
    ) {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
    }

    public function getTFLStatusQuery(): StatusQuery
    {
        return new StatusQuery(
            $this->httpClient,
            'dd159361',
            'c5657f306c94676a4f297ec22c070fc3' // @todo - reset this key with TFL (silly me checked it into github)
        );
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
