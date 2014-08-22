<?php
namespace Pinq\Caching;

/**
 * Cache interface that prefixes all keys with the specified namespace.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface INamespacedCacheAdapter extends ICacheAdapter
{
    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return ICacheAdapter
     */
    public function getInnerCache();
}
