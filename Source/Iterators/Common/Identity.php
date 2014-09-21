<?php

namespace Pinq\Iterators\Common;

/**
 * Utility class for hashing the identity of any value as a string.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Identity
{
    private function __construct()
    {

    }

    /**
     * Returns a string representing the supplied value's identity.
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function hash($value)
    {
        $typeIdentifier = gettype($value)[0];

        switch ($typeIdentifier) {

            case 's': //string

                return 's' . (strlen($value) > 32 ? md5($value) : $value);

            case 'i': //integer
            case 'b': //boolean
            case 'd': //double
            case 'r': //resource
            case 'u': //unknown type

                return $typeIdentifier . $value;

            case 'N': //NULL

                return 'N';

            case 'o': //object

                return 'o' . spl_object_hash($value);

            case 'a': //array

                return self::arrayHash($value);
        }
    }

    /**
     * Returns an array of string representations of the supplied values
     *
     * @param mixed[] $values
     *
     * @return string[]
     */
    public static function hashAll(array $values)
    {
        return array_map([__CLASS__, 'hash'], $values);
    }

    private static function arrayHash(array $array)
    {
        array_walk_recursive(
                $array,
                function (&$value) {
                    $value = self::hash($value);
                }
        );

        return 'a' . md5(serialize($array));
    }
}
