<?php

namespace TubeService\Service;

class ServiceResultEmpty extends ServiceResult
{
    public function __construct()
    {
        parent::__construct(
            [],
            0
        );
    }
}
