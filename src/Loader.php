<?php

namespace Pmaxs\Config;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Pmaxs\Config\Processor\ProcessorInterface;

/**
 * Class Loader
 */
class Loader extends DelegatingLoader
{
    /**
     * @var array processors
     */
    protected $processors;

    /**
     * Constructor.
     * @param mixed $paths paths to configs
     */
    public function __construct($paths = null)
    {
        // resolver
        $this->resolver = new LoaderResolver();

        if (!empty($paths)) {
            $loaders = $this->getDefaultFileLoaders(new FileLocator((array)$paths));

            foreach ($loaders as $loader) $this->resolver->addLoader($loader);
        }

        // processors
        $this->processors = $this->getDefaultProcessors();
    }

    /**
     * {@inheritdoc}
     * @param mixed $resources
     */
    public function load($resources, $type = null)
    {
        $config = new Config();

        foreach ((array)$resources as $resource) {
            $config->merge(parent::load($resource, $type));
        }

        foreach ($this->processors as $processor) {
            $processor->process($config);
        }

        return $config;
    }

    /**
     * Adds a loader.
     * @param LoaderInterface $loader A LoaderInterface instance
     * @return $this
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->resolver->addLoader($loader);
        return $this;
    }

    /**
     * Clears loaders.
     * @return $this
     */
    public function clearLoaders()
    {
        $this->resolver = new LoaderResolver();
        return $this;
    }

    /**
     * Returns the registered loaders.
     * @return LoaderInterface[] An array of LoaderInterface instances
     */
    public function getLoaders()
    {
        return $this->resolver->getLoaders();
    }

    /**
     * Adds processor.
     * @param ProcessorInterface $processor
     * @return $this
     */
    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
        return $this;
    }

    /**
     * Clears processors.
     * @return $this
     */
    public function clearProcessors()
    {
        $this->processors = array();
        return $this;
    }

    /**
     * Returns processors.
     * @return array
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Returns default loaders.
     * @return array
     */
    public function getDefaultFileLoaders(FileLocatorInterface $locator)
    {
        return array(
            new \Pmaxs\Config\Loader\YamlLoader($locator),
            new \Pmaxs\Config\Loader\IniLoader($locator),
            new \Pmaxs\Config\Loader\XmlLoader($locator),
        );
    }

    /**
     * Returns default processors.
     * @return array
     */
    public function getDefaultProcessors()
    {
        return array(
            new \Pmaxs\Config\Processor\Constant(),
            new \Pmaxs\Config\Processor\Parameter(),
        );
    }
}
