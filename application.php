#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Dry\Console\Command\DryCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new DryCommand);
$application->run();