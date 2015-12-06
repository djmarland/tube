<?php

namespace TubeService\Data\TFL\Mapper;

abstract class Mapper
{
    protected $mapperFactory;

    public function __construct(
        MapperFactory $mapperFactory
    ) {
        $this->mapperFactory = $mapperFactory;
    }
}
