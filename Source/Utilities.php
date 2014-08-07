<?php

namespace Pinq;

/**
 * General utility class providing common and misc behaviour
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
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
     *
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
     *
     * @return boolean Whether the value is iterable
     */
    public static function isIterable($value)
    {
        return $value instanceof \Traversable || is_array($value);
    }
}
