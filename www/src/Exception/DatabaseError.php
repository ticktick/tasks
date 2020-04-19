<?php

namespace Core\Exception;

class DatabaseError extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}