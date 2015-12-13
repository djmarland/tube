<?php

namespace IngestService\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use IngestService\Domain\TFLLine;
use TubeService\Data\Database\Entity\Status;
use TubeService\Domain\Entity\Line;

class UpdateService
{
    const TBL = 'tbl';

    protected $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function addNewStatus(
        Line $line,
        TFLLine $TFLLine
    ) {
        // get the original lineEntity out of the database
        $lineEntity = $this->entityManager
            ->find('TubeService:Line', $line->getId());

        // create a new status for this line
        $status = new Status();
        $status->setLine($lineEntity);
        $description = null;
        $descriptions = $TFLLine->getStatusDescriptions();
        if ($descriptions) {
            $description = implode('|', $descriptions);
        }

        $status->setDescription($description);
        $status->setIsDisrupted($TFLLine->isDisrupted());
        $status->setTitle($TFLLine->getStatusTitle());
        $status->setShortTitle($TFLLine->getStatusShortTitle());

        $this->entityManager->persist($status);
        $this->entityManager->flush();

        // use the new status ID to update the line
        $lineEntity->setLatestStatus($status);
        $this->entityManager->persist($lineEntity);
        $this->entityManager->flush();
    }
}