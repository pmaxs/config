<?php
require '../vendor/autoload.php';

define('CONSTANT', 'This is constant');

$loader = new \Pmaxs\Config\Loader('./config/simple');
$config = $loader->load('config.yml');

echo "<pre>";

echo "Config:";
if (function_exists('dump')) dump($config->getData());
else var_dump($config->getData());

echo "Resources:";
if (function_exists('dump')) dump($config->getResources());
else var_dump($config->getResources());

echo "</pre>";
