<?php

namespace TubeService\Service;

//use DatabaseBundle\Mapper\MapperFactory;
//use Doctrine\ORM\EntityManager;
//use Doctrine\ORM\EntityRepository;

use GuzzleHttp\ClientInterface;
use TubeService\Data\TFL\Query\StatusQuery;

abstract class Service
{
    protected $httpClient;

    protected $entityManager;

    protected $mapperFactory;

    public function __construct(
        ClientInterface $httpClient,
        EntityManager $entityManager = null,
        MapperFactory $mapperFactory = null
    ) {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->mapperFactory = $mapperFactory;
    }

    public function getEntity(string $name): EntityRepository
    {
        return $this->entityManager
            ->getRepository('DatabaseBundle:' . $name);
    }

    public function getTFLStatusQuery(): StatusQuery
    {
        return new StatusQuery(
            $this->httpClient,
            'dd159361',
            'c5657f306c94676a4f297ec22c070fc3'
        );
    }

    public function getQueryBuilder(string $name) {
        $entity = $this->getEntity($name);
        return $entity->createQueryBuilder('tbl');
    }

    public function getDomainModels($items)
    {
        if (!$items) {
            return null;
        }
        if (!is_array($items)) {
            $items = [$items];
        }

        $domainModels = array();
        foreach ($items as $item) {
            $mapper = $this->mapperFactory->getMapper($item);
            $domainModels[] = $mapper->getDomainModel($item);
        }
        return $domainModels;
    }

    public function getOffset(
        int $limit,
        int $page
    ): int {
        return ($limit * ($page - 1));
    }

    public function getServiceResult($result)
    {
        $data = $this->getDomainModels($result);

        if ($data) {
            return new ServiceResult($data);
        }
        return new ServiceResultEmpty();
    }
}
