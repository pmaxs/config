<?php
require '../vendor/autoload.php';

define('DEV', 0);
define('CONSTANT', 'This is constant');

$cache_path = './cache/config.php';

$cache = new \Symfony\Component\Config\ConfigCacheFactory(DEV);

$cache->cache($cache_path, function ($cache) {
    $loader = new \Pmaxs\Config\Loader('./config/simple');

    $config = $loader->load('config.yml');

    $cache->write('<?php return ' . var_export($config->getData(), 1) . ';', $config->getResources());
});

$config = include $cache_path;

echo "<pre>";

echo "Config:";
if (function_exists('dump')) dump($config);
else var_dump($config);

echo "</pre>";
