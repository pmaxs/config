<?php

namespace Pmaxs\Config\Loader;

/**
 * Class XmlLoader
 */
class XmlLoader extends ExplicitLoader
{
    /**
     * {@inheritdoc}
     */
    const READER = '\\Zend\\Config\\Reader\\Xml::fromFile';

    /**
     * {@inheritdoc}
     */
    const RESOURCE_EXTENSIONS = 'xml';
}
