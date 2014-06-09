<?php

namespace Pinq\Iterators\Common;

/**
 * Utility class for hashing the identity of any value as a string.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
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
     * @return string
     */
    public static function hash($value)
    {
        $type = gettype($value);
        
        $typeIdentifier = $type[0];
        switch ($type) {

            case 'string':
            case 'integer':
            case 'boolean':
            case 'double':
            case 'resource':
            case 'unknown type':
                return $typeIdentifier . $value;
                
            case 'NULL':
                return 'N';
                
            case 'object':
                return 'o' . spl_object_hash($value);
                
            case 'array':
                return self::arrayHash($value);
        }
    }
    
    private static function arrayHash(array $array)
    {
        array_walk_recursive($array, function (&$value) {
            $value = self::hash($value);
        });
        
        $hashData = serialize($array);
        return 'a' . (strlen($hashData) > 32 ? md5($hashData) : $hashData);
    }
}
