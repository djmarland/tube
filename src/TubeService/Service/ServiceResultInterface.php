<?php

namespace TubeService\Service;

interface ServiceResultInterface
{
    public function setTotal(int $total);
    public function setDomainModels(array $models);

    public function isEmpty();

    public function getTotal();
    public function getDomainModels();
    public function getDomainModel();
}
