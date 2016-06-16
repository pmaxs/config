<?php

namespace Pmaxs\Config\Processor;

use Pmaxs\Config\Config;

/**
 * Class Parameter
 * Code was taken from \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
 */
class Parameter implements ProcessorInterface
{
    protected $parameters = array();
    protected $resolved = false;

    /**
     * {@inheritdoc}
     */
    public function process(Config $config)
    {
        $data = $config->getData();
        $propertyAccessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();

        $flatten = array();
        $this->flattenArray($data, $flatten);

        $parameters = array();
        foreach ($flatten as $k => $v) $parameters[$k] = $v['value'];

        $this->clear();
        $this->add($parameters);
        $this->resolved = false;
        $this->resolve();

        $parameters = $this->all();

        foreach ($parameters as $k => $v) {
            if ($v === $flatten[$k]['value']) continue;

            $propertyAccessor->setValue($data, $flatten[$k]['path'], $v);
        }

        $config->setData($data);

        return $config;
    }

    public function flattenArray($array, &$flatten, $index = null, $path = null)
    {
        if (\is_array($array)) {
            foreach ($array as $k => $v) {
                $this->flattenArray(
                    $v,
                    $flatten,
                    (isset($index) ? $index . '.' : '') . $k,
                    (isset($path) ? $path : '') . '[' . $k . ']'
                );
            }
        } else {
            $flatten[$index] = array(
                'path' => $path,
                'value' => $array,
            );
        }
    }

    // ---------------------------------------------------------------
    // Code was taken from
    // \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
    // ---------------------------------------------------------------

    public function clear()
    {
        $this->parameters = array();
    }

    public function add(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    public function all()
    {
        return $this->parameters;
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \Exception('Parameter "' . $name . '" not found');
        }

        return $this->parameters[$name];
    }

    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    public function remove($name)
    {
        unset($this->parameters[$name]);
    }

    public function resolve()
    {
        if ($this->resolved) {
            return;
        }

        $parameters = array();
        foreach ($this->parameters as $key => $value) {
            try {
                $value = $this->resolveValue($value);
                $parameters[$key] = $this->unescapeValue($value);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        $this->parameters = $parameters;
        $this->resolved = true;
    }

    public function resolveValue($value, array $resolving = array())
    {
        if (is_array($value)) {
            $args = array();
            foreach ($value as $k => $v) {
                $args[$this->resolveValue($k, $resolving)] = $this->resolveValue($v, $resolving);
            }

            return $args;
        }

        if (!is_string($value)) {
            return $value;
        }

        return $this->resolveString($value, $resolving);
    }

    public function resolveString($value, array $resolving = array())
    {
        if (preg_match('/^%([^%\s]+)%$/', $value, $match)) {
            $key = $match[1];

            if (isset($resolving[$key])) {
                throw new \Exception('Circular references: ' . implode(', ', array_keys($resolving)));
            }

            $resolving[$key] = true;

            return $this->resolved ? $this->get($key) : $this->resolveValue($this->get($key), $resolving);
        }

        return preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($resolving, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }

            $key = $match[1];
            if (isset($resolving[$key])) {
                throw new \Exception('Circular references: ' . implode(', ', array_keys($resolving)));
            }

            $resolved = $this->get($key);

            if (!is_string($resolved) && !is_numeric($resolved)) {
                throw new \Exception(sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type %s inside string value "%s".', $key, gettype($resolved), $value));
            }

            $resolved = (string)$resolved;
            $resolving[$key] = true;

            return $this->isResolved() ? $resolved : $this->resolveString($resolved, $resolving);
        }, $value);
    }

    public function isResolved()
    {
        return $this->resolved;
    }

    public function escapeValue($value)
    {
        if (is_string($value)) {
            return str_replace('%', '%%', $value);
        }

        if (is_array($value)) {
            $result = array();
            foreach ($value as $k => $v) {
                $result[$k] = $this->escapeValue($v);
            }

            return $result;
        }

        return $value;
    }

    public function unescapeValue($value)
    {
        if (is_string($value)) {
            return str_replace('%%', '%', $value);
        }

        if (is_array($value)) {
            $result = array();
            foreach ($value as $k => $v) {
                $result[$k] = $this->unescapeValue($v);
            }

            return $result;
        }

        return $value;
    }
}
