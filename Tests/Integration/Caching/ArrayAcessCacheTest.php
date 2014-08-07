<?php

namespace Pinq\Tests\Integration\Caching;

use Pinq\Caching\ArrayAccessCacheAdapter;

class ArrayAcessCacheTest extends CacheTest
{
    protected function setUp()
    {
        $this->cache = new ArrayAccessCacheAdapter(new ArrayAccessCacheImplementation());
    }
}
