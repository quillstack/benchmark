#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use QuillBenchamrk\Commands\ConsoleBenchmarkCommand;
use QuillBenchamrk\Commands\HttpGetBenchmarkCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new HttpGetBenchmarkCommand());
$application->add(new ConsoleBenchmarkCommand());
$application->run();
