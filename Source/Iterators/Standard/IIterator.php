<?php

namespace Pinq\Iterators\Standard;

/**
 * An extended \Iterator interface for simplicity and performance:
 * Native iterator code:
 * <code>
 * $iterator->rewind();
 * while ($iterator->valid()) {
 *     $key = $iterator->key();
 *     $value = $iterator->current();
 *     ...
 *     $iterator->next();
 * }
 * </code>
 * With Pinq extension:
 * <code>
 * $iterator->rewind();
 * while (list($key, $value) = $iterator->fetch()) {
 *     ...
 * }
 * </code>
 * Or with value by reference:
 * <code>
 * $iterator->rewind();
 * while ($element = $iterator->fetch()) {
 *     $key = $element[0];
 *     $value =& $element[1];
 *     ...
 * }
 * </code>
 * Iterators can implement this interface while maintaining
 * compatibility with the native API.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IIterator extends \Iterator, \Pinq\Iterators\IIterator
{
    const IITERATOR_TYPE = __CLASS__;

    /**
     * If the current position is valid, returns an array with
     * index zero as the key and index one as the value and advances
     * the iterator to the next position or returns null if the current
     * position is invalid.
     *
     * @return array|null The element array or null if invalid position
     */
    public function fetch();
}
