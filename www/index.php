<?php
require __DIR__ . '/vendor/autoload.php';

use Core\Application;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(__DIR__.'/config/common.yml'));
$application = new Application($config);
$application->run();
