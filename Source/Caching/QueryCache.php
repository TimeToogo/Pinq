<?php

namespace Pinq\Caching;

use Pinq\Queries\IQuery;

/**
 * Cache implementation that acts as wrapper and second level cache
 * for the underlying cache implementation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class QueryCache implements IQueryCache
{
    /**
     * The underlying cache implementation.
     *
     * @var ICacheAdapter
     */
    private $cacheAdapter;

    private static $secondLevelCache = [];

    public function __construct(ICacheAdapter $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
    }

    public function save($hash, IQuery $query)
    {
        self::$secondLevelCache[$hash] = $query;
        $this->cacheAdapter->save($hash, $query);
    }

    public function tryGet($hash)
    {
        if (!isset(self::$secondLevelCache[$hash])) {
            self::$secondLevelCache[$hash] = $this->cacheAdapter->tryGet($hash) ?: null;
        }

        $cachedValue = self::$secondLevelCache[$hash];

        return $cachedValue instanceof IQuery ? $cachedValue : null;
    }

    public function clear()
    {
        self::$secondLevelCache = [];
        $this->cacheAdapter->clear();
    }

    public function remove($hash)
    {
        unset(self::$secondLevelCache[$hash]);
        $this->cacheAdapter->remove($hash);
    }
}
