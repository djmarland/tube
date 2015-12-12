<?php

namespace TubeService\Data\Database\Mapper;

abstract class Mapper
{
    protected $mapperFactory;

    public function __construct(
        MapperFactory $mapperFactory
    ) {
        $this->mapperFactory = $mapperFactory;
    }
}
