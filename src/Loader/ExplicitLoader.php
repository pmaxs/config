<?php

namespace Pmaxs\Config\Loader;

/**
 * Class ExplicitLoader
 */
abstract class ExplicitLoader extends FileLoader
{
    /**
     * Reader
     */
    const READER = null;

    /**
     * Config file extensions
     */
    const RESOURCE_EXTENSIONS = null;

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        if (!\file_exists($path)) throw new \Exception('Config "' . $path . '" not found');

        $reader = $this->getReader();
        if (!\is_callable($reader)) throw new \Exception('Config reader is not defined or is not callable');

        $config = \call_user_func_array($reader, array($path));

        return $config;
    }

    /**
     * Returns config reader
     * @return callable
     */
    public function getReader()
    {
        if (!static::READER) return null;

        if (\strpos(static::READER, '::') === false) return static::READER;

        $reader = \explode('::', static::READER, 2);

        if (!((new \ReflectionClass($reader[0]))->getMethod($reader[1])->isStatic()))
            $reader[0] = new $reader[0]();

        return $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return
            \is_string($resource)
            && \in_array(
                \pathinfo($resource, \PATHINFO_EXTENSION),
                \preg_split('~\\s,;~', static::RESOURCE_EXTENSIONS)
            );
    }
}
