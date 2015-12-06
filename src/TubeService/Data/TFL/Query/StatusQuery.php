<?php

namespace TubeService\Data\TFL\Query;


use TubeService\Data\TFL\Mapper\MapperFactory;

class StatusQuery extends Query
{

    const BASE_URL = 'https://api.tfl.gov.uk/Line/Mode/tube,dlr,tflrail,overground/Status';

    public function getAllLines()
    {
        $data = $this->getData($this->getUrl());
        $mapperFactory = new MapperFactory();
        $lines = [];
        foreach ($data as $lineData) {
            $line = $mapperFactory->getDomainModel($lineData);
            if ($line) {
                $lines[] = $line;
            }
        }
        return $lines;
    }

}