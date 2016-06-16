<?php

namespace Pmaxs\Config\Loader;

/**
 * Class IniLoader
 */
class IniLoader extends ExplicitLoader
{
    /**
     * {@inheritdoc}
     */
    const READER = '\\Zend\\Config\\Reader\\Ini::fromFile';

    /**
     * {@inheritdoc}
     */
    const RESOURCE_EXTENSIONS = 'ini';
}
