<?php

namespace App\Classes\Factory;

use App\Exceptions\NamespaceNotFoundException;
use Psr\Container\ContainerInterface;

class MapFactory implements FactoryInterface, ContainerInterface
{
    protected array $container = [];

    protected array $cache = [];

    public function __construct(array $container)
    {
        $this->container = $container;
    }

    public function get(string $id)
    {
        if (! isset($this->cache[$id])) {
            $this->cache[$id] = $this->make($id);
        }

        return $this->cache[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->container[$id]);
    }

    /**
     * @throw NamespaceNotFoundException
     */
    public function make(string $id)
    {
        if ($this->has($id)) {
            throw new NamespaceNotFoundException(
                sprintf("Undefined map name %s", $id)
            );
        }

        $class = $this->container[$id];

        return new $class();
    }
}
