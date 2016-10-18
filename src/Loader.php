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
     * @var array vars
     */
    protected $vars = [];

    /**
     * Constructor.
     * @param mixed $paths paths to configs
     * @param mixed $vars init vars
     */
    public function __construct($paths = null, array $vars = [])
    {
        // resolver
        $this->resolver = new LoaderResolver();

        if (!empty($paths)) {
            $loaders = $this->getDefaultFileLoaders(new FileLocator((array)$paths));

            foreach ($loaders as $loader) $this->resolver->addLoader($loader);
        }

        // processors
        $this->processors = $this->getDefaultProcessors();

        // vars
        $this->addVars($vars);
    }

    /**
     * @inheritdoc
     * @param mixed $resources
     */
    public function load($resources, $type = null)
    {
        $config = new Config();
        $config->setData($this->vars);

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

    /**
     * Adds vars.
     * @param array $vars
     * @return $this
     */
    public function addVars(array $vars = [])
    {
        $this->vars = array_merge($this->vars, $vars);
        return $this;
    }

    /**
     * Clears vars.
     * @return $this
     */
    public function clearVars()
    {
        $this->vars = [];
        return $this;
    }

    /**
     * Returns vars.
     * @return array vars
     */
    public function getVars()
    {
        return $this->vars;
    }
}
