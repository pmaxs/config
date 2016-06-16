<?php

namespace Pmaxs\Config\Loader;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Pmaxs\Config\Config;

/**
 * Class FileLoader
 */
abstract class FileLoader extends BaseFileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $paths = $this->getLocator()->locate($resource, null, false);
        if (empty($paths)) throw new \Exception('Config resource "' . $resource . '" not found');

        $config = new Config();

        foreach ((array)$paths as $path) {
            $config->addResource(new FileResource($path));
            $this->processData($config, $this->read($path), $type);
        }

        return $config;
    }

    /**
     * Parse config
     * @param Config $config
     * @param array $data
     * @param $type
     * @return $this
     */
    protected function processData(Config $config, $data, $type = null)
    {
        if (\array_key_exists('imports', $data)) {
            $import = $data['imports'];
            unset($data['imports']);
        }

        if (!empty($import) && \is_array($import)) {
            if (isset($import['resource'])) {
                $config1 = $this->import($import['resource'], $type);
                $config->merge($config1, isset($import['prefix']) ? $import['prefix'] : null);
            } else {
                foreach ($import as $import1) {
                    $config1 = $this->import($import1['resource'], $type);
                    $config->merge($config1, isset($import1['prefix']) ? $import1['prefix'] : null);
                }
            }
        }

        $config->addData($data);

        return $this;
    }
}
