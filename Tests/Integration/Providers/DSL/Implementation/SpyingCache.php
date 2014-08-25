<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation;

use Pinq\Caching\ArrayAccessCacheAdapter;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SpyingCache extends ArrayAccessCacheAdapter
{
    /**
     * @var \ArrayObject
     */
    private $arrayObject;

    public function __construct()
    {
        $this->arrayObject = new \ArrayObject();
        parent::__construct($this->arrayObject);
    }

    /**
     * @return mixed[]
     */
    public function getCachedArray()
    {
        return $this->arrayObject->getArrayCopy();
    }
} 