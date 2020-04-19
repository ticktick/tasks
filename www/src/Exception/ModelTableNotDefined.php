<?php

namespace Core\Exception;

class ModelTableNotDefined extends \Exception
{
    protected $message = 'В модели не задана таблица';
}