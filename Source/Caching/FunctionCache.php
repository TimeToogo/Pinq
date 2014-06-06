<?php

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * Cache implementation that acts as wrapper and second level cache 
 * for the underlying cache implementation.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionCache implements IFunctionCache
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

    public function save($functionHash, FunctionExpressionTree $functionExpressionTree)
    {
        self::$secondLevelCache[$functionHash] = clone $functionExpressionTree;
        $this->cacheAdapter->save($functionHash, $functionExpressionTree);
    }

    public function tryGet($functionHash)
    {
        if (!isset(self::$secondLevelCache[$functionHash])) {
            self::$secondLevelCache[$functionHash] = $this->cacheAdapter->tryGet($functionHash) ?: null;
        }

        $cachedValue = self::$secondLevelCache[$functionHash];

        return $cachedValue === null ? null : clone $cachedValue;
    }

    public function clear()
    {
        self::$secondLevelCache = [];
        $this->cacheAdapter->clear();
    }

    public function remove($functionHash)
    {
        unset(self::$secondLevelCache[$functionHash]);
        $this->cacheAdapter->remove($functionHash);
    }
}
