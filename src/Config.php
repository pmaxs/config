<?php

namespace Pmaxs\Config;

/**
 * Class Config
 */
class Config
{
    /**
     * Data
     * @var array
     */
    protected $data = array();

    /**
     * Resources
     * @var array
     */
    protected $resources = array();

    /**
     * Return data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data
     * @param array $data data
     * @return $this
     */
    public function setData(array $data)
    {
        return $this->data = $data;
    }

    /**
     * Return data
     * @param array $data data to add
     * @param string $prefix prefix
     * @return $this
     */
    public function addData(array $data, $prefix = null)
    {
        if (isset($prefix) && \strlen($prefix)) $data = array($prefix => $data);

        $this->data = \array_replace_recursive($this->data, $data);

        return $this;
    }

    /**
     * Return resources
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Add resource
     * @param string $resource
     * @return $this
     */
    public function addResource($resource)
    {
        $this->resources[] = $resource;
        return $this;
    }

    /**
     * Add resource
     * @param array $resources
     * @return $this
     */
    public function addResources(array $resources)
    {
        $this->resources = \array_merge($this->resources, $resources);
        return $this;
    }

    /**
     * @param Config $config
     * @param string $prefix prefix
     * @return $this
     */
    public function merge(Config $config, $prefix = null)
    {
        $this->addResources($config->getResources());

        $this->addData($config->getData(), $prefix);

        return $this;
    }
}
