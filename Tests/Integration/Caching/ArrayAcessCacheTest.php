<?php

namespace Pinq\Tests\Integration\Caching;

use \Pinq\Caching\ArrayAccessCache;

class ArrayAcessCacheTest extends CacheTest
{
    protected function setUp()
    {
        $this->Cache = new ArrayAccessCache(new \ArrayObject());
    }
}
