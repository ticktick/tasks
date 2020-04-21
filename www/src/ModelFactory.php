<?php

namespace Core;

use Core\Exception\ModelNotExists;

class ModelFactory {

    private $dbconfig;

    public function __construct(array $dbconfig)
    {
        $this->dbconfig = $dbconfig;
    }

    public function getModel(string $class): ModelInterface
    {
        if(!class_exists($class)){
            throw new ModelNotExists($class);
        }
        return new $class($this->dbconfig);
    }
}