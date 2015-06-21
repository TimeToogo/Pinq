<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedMap;

/**
 * Common functionality for the array compatible iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait ArrayCompatibleIterator
{
    protected function makeKeyCompatible(&$key, &$maxKey, IOrderedMap $incompatibleKeyMap)
    {
        if (is_int($key) || is_string($key)) {
            //Integer strings like "123" get auto cast to integers when set as array keys
            $intKey = (int) $key;
            if ((string) $intKey === (string) $key && $intKey >= $maxKey) {
                $maxKey = $intKey + 1;
            }
        } elseif ($incompatibleKeyMap->contains($key)) {
            $key = $incompatibleKeyMap->get($key);
        } else {
            $originalKey = $key;
            $key         = $maxKey++;
            $incompatibleKeyMap->set($originalKey, $key);
        }
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return true;
    }
}
