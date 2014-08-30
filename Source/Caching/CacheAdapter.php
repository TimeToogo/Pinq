<?php

namespace Pinq\Caching;

/**
 * Base class for cache implementations
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class CacheAdapter implements ICacheAdapter
{
    /**
     * @var string|null
     */
    protected $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    public function hasNamespace()
    {
        return $this->namespace !== null;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }
}
