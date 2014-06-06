<?php

namespace Pinq\Iterators\Standard;

/**
 * An extended \Iterator interface for simplicity and performance:
 * 
 * Native iterator code:
 * <code>
 * $iterator->rewind();
 * while($iterator->valid()) {
 *     $key = $iterator->key();
 *     $value = $iterator->current();
 *     ...
 *     $iterator->next();
 * }
 * </code>
 * 
 * With Pinq extension:
 * <code>
 * $iterator->rewind();
 * while($iterator->fetch($key, $value)) {
 *     ...
 * }
 * </code>
 * 
 * Iterators can implement this interface while maintaining 
 * compatibility with the native API.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IIterator extends \Iterator
{
    const IITERATOR_TYPE = __CLASS__;
    
    /**
     * Returns whether the current position is valid and if so,
     * sets the current key and value to the the respective parameters
     * and advances the iterator to the next position.
     * 
     * @param mixed &$key The key variable reference
     * @param mixed &$value The value variable reference
     * @return boolean Whether the current position is valid
     */
    public function fetch(&$key, &$value);
}
