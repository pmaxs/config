<?php

namespace Pmaxs\Config\Processor;

use Pmaxs\Config\Config;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    /**
     * Process Config
     *
     * @param  Config $value
     * @return Config
     */
    public function process(Config $config);
}
