<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedMap;

/**
 * Common functionality for the array compatible iterator
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait ArrayCompatibleIterator
{
    protected function makeKeyCompatible(&$key, &$maxKey, IOrderedMap $nonScalarKeyMap)
    {
        if($key === null || is_scalar($key)) {
            $intKey = (int)$key;
            if((string)$intKey === (string)$key && $intKey >= $maxKey) {
                $maxKey = $intKey + 1;
            }
        } elseif($nonScalarKeyMap->contains($key)) {
            $key = $nonScalarKeyMap->get($key);
        } else{
            $originalKey = $key;
            $key = $maxKey++;
            $nonScalarKeyMap->set($originalKey, $key);
        }
    }
}
