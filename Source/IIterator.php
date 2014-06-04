<?php

namespace Pinq;

/**
 * Extending the native Iterator implementation for simplicity and performance:
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
 * The iterators included in this library implement this interface
 * while maintaining compatibility with the native interface.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IIterator extends \Iterator
{
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
