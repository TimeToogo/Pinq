<?php

namespace Pinq;

/**
 * General utility class providing common and misc behaviour
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Utilities
{
    private function __construct()
    {

    }

    /**
     * Returns the type or class of the supplied function
     *
     * @param mixed $value The value
     * @return string The type or class
     */
    public static function getTypeOrClass($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    /**
     * Returns whether the value is iterable
     *
     * @param mixed $value The value
     * @return boolean Whether the value is iterable
     */
    public static function isIterable($value)
    {
        return $value instanceof \Traversable || is_array($value);
    }

    /**
     * Returns the iterator as an array.
     *
     * @param \Traversable $iterator The iterator value
     * @return array
     */
    public static function toArray(\Traversable $iterator)
    {
        if ($iterator instanceof \ArrayIterator || $iterator instanceof \ArrayObject) {
            return $iterator->getArrayCopy();
        }

        $array = [];
        $iterator = self::toIterator($iterator);
        //Does not support returning custom keys
        $iterator->rewind();

        while ($iterator->valid()) {
            $key = $iterator->key();
            if($key === null || is_scalar($key)) {
                $array[$key] = $iterator->current();
            } else {
                $array[] = $iterator->current();
            }
            $iterator->next();
        }

        return $array;
    }

    /**
     * Returns the values as an iterator
     *
     * @param array|\Traversable $traversableOrArray The value
     * @return \Iterator
     * @throws PinqException If the value is not a array nor \Traversable
     */
    public static function toIterator($traversableOrArray)
    {
        if (!self::isIterable($traversableOrArray)) {
            throw PinqException::invalidIterable(__METHOD__, $traversableOrArray);
        }

        if ($traversableOrArray instanceof \Iterator) {
            return $traversableOrArray;
        } elseif ($traversableOrArray instanceof \IteratorAggregate) {
            return $traversableOrArray->getIterator();
        } elseif ($traversableOrArray instanceof \Traversable) {
            return new \IteratorIterator($traversableOrArray);
        } else {
            return new \ArrayIterator($traversableOrArray);
        }
    }

    /**
     * Returns whether the supplied name is cosidered normal name syntax
     * and can be used plainly in code.
     *
     * Example:
     * 'foo' -> yes: $foo
     * 'foo bar' -> no: ${'foo bar'}
     *
     * @param string $name The field, function, method or variable name
     * @return boolean
     */
    public static function isNormalSyntaxName($name)
    {
        return (bool)preg_match('/[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*/', $name);
    }
}
