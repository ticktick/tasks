<?php

namespace Core\Exception;

class ControllerNotExists extends \Exception
{
    protected $message = 'Неизвестный контроллер';
}