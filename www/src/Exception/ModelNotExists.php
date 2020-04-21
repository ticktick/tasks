<?php

namespace Core\Exception;

class ModelNotExists extends \Exception
{
    public function __construct(string $modelName)
    {
        parent::__construct('Модель не обнаружена: ' . $modelName);
    }
}