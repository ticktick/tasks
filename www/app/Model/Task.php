<?php

namespace App\Model;

use Core\Model;
use Core\ModelInterface;

class Task extends Model implements ModelInterface
{

    const STATUS_TODO = 'todo';
    const STATUS_DONE = 'done';

    protected $table = 'task';

}