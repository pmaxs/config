<?php

namespace Pmaxs\Config;

/**
 * Class GConfig=
 */
class GConfig
{
    /**
     * Data
     * @var array
     */
    protected static $data;

    /**
     * Property accessor
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected static $accessor;

    /**
     * Return data
     * @return mixed
     */
    public static function get($name)
    {
        return self::$accessor->getValue(self::$data, $name);
    }

    /**
     * Inits GConfig with data data
     * @param mixed $data
     * @return array
     */
    public static function init($data)
    {
        self::$data = $data;
        self::$accessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
    }
}
