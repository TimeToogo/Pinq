<?php

namespace Pinq;

final class Utilities
{
    private function __construct() { }

    public static $Identical = [__CLASS__, 'Identical'];

    public static function Identical($One, $Two)
    {
        return $One === $Two;
    }
    
    public static function ToArray(\Traversable $Iterator)
    {
        if($Iterator instanceof \ArrayIterator || $Iterator instanceof \ArrayObject) {
            return $Iterator->getArrayCopy();
        }
        
        return iterator_to_array($Iterator);
    }
    
    public static function MultisortPreserveKeys(array $OrderArguments, array &$ArrayToSort)
    {
        $StringKeysArray = [];
        foreach ($ArrayToSort as $Key => $Value) {
            $StringKeysArray['a' . $Key] = $Value;
        }
        
        $OrderArguments[] =& $StringKeysArray;
        call_user_func_array('array_multisort', $OrderArguments);
        
        $UnserializedKeyArray = [];
        foreach ($StringKeysArray as $Key => $Value) {
            $UnserializedKeyArray[substr($Key, 1)] = $Value;
        }
        
        $ArrayToSort = $UnserializedKeyArray;
    }
}
