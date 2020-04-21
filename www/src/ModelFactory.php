<?php

namespace Core;

class ModelFactory {

    private $dbconfig;

    public function __construct(array $dbconfig)
    {
        $this->dbconfig = $dbconfig;
    }

    public function getModel(string $class): ModelInterface
    {
        return new $class($this->dbconfig);
    }
}