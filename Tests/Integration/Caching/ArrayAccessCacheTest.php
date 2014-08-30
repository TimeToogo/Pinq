<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\ArrayAccessCacheAdapter;

class ArrayAccessCacheTest extends CacheTest
{
    protected function setUpCache()
    {
        return new ArrayAccessCacheAdapter(new ArrayAccessCacheImplementation());
    }
}
