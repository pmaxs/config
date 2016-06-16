<?php

namespace Pmaxs\Config\Loader;

/**
 * Class YamlLoader
 */
class YamlLoader extends ExplicitLoader
{
    /**
     * @inheritdoc
     */
    const READER = '\\Zend\\Config\\Reader\\Yaml::fromFile';

    /**
     * {@inheritdoc}
     */
    const RESOURCE_EXTENSIONS = 'yml';

    /**
     * {@inheritdoc}
     */
    public function getReader()
    {
        if (\function_exists('yaml_parse')) {
            $parser = 'yaml_parse';
        } else {
            $parser = array('\\Symfony\\Component\\Yaml\\Yaml', 'parse');
        }

        $reader = \explode('::', static::READER, 2);

        $reader[0] = new $reader[0]($parser);

        return $reader;
    }
}
