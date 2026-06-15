<?php

declare(strict_types=1);

/** @var \App\Container $container */
$container = require __DIR__ . '/../bootstrap.php';

$container->surveyController()->handle();
