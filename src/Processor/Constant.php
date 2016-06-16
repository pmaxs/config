<?php

namespace Pmaxs\Config\Processor;

use Pmaxs\Config\Config;

/**
 * Class Constant
 */
class Constant implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(Config $config)
    {
        $map = $this->getMap();
        $data = $config->getData();

        \array_walk_recursive($data, function(&$v, $k) use ($map) {
            $v = \strtr($v, $map);
        });

        $config->setData($data);

        return $config;
    }

    /**
     * Returns constants map for replace
     * @return array
     */
    private function getMap()
    {
        static $map;
        if (!isset($map)) $map = $constants = \get_defined_constants();

        return $map;
    }
}
