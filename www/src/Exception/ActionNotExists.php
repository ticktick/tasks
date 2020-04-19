<?php

namespace Core\Exception;

class ActionNotExists extends \Exception
{
    protected $message = 'Неизвестный экшн';
}