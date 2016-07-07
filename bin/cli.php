<?php

if (PHP_SAPI !== 'cli') {
    throw new ErrorException('Endpoint only available through Comand Line Interface.');
}

require __DIR__ . '/../vendor/autoload.php';

$bootstap = new \App\Core\Bootstrap\CliBootstrap();
$bootstap->run();