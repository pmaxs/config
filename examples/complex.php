<?php
require '../vendor/autoload.php';

define('CONSTANT1', 'This is constant 1');
define('CONSTANT2', 'This is constant 2');
define('CONSTANT3', 'This is constant 3');

$loader = new \Pmaxs\Config\Loader(['./config/complex/1', './config/complex/2']);
$config = $loader->load(['config.yml']);

echo "<pre>";

echo "Config:";
if (function_exists('dump')) dump($config->getData());
else var_dump($config->getData());

echo "Resources:";
if (function_exists('dump')) dump($config->getResources());
else var_dump($config->getResources());

echo "</pre>";
