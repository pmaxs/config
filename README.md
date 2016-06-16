# Config

Library for loading configs (yaml, xml, ini) based on Symfony Config component.
Supports imports, constants and parameters (Symfony DI style %parameter%).

Installation
------------

    composer require pmaxs/config

Usage
-----

```php
require '../vendor/autoload.php';

define('CONSTANT', 'This is constant');

$loader = new \Pmaxs\Config\Loader('./config');
$config = $loader->load('config.yml')->getData();
```
