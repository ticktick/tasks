<?php
require __DIR__ . '/vendor/autoload.php';

use Core\Application;

$config = null;
$application = new Application($config);
$application->run();
