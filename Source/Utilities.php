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

    public static $Comparison = [__CLASS__, 'Compare'];

    public static function Compare($One, $Two)
    {
        if($One === $Two || $One == $Two) {
            return 0;
        }
        
        return $One > $Two ? 1 : -1;
    }
    
    public static function ToArray(\Traversable $Iterator)
    {
        if($Iterator instanceof \ArrayIterator || $Iterator instanceof \ArrayObject) {
            return $Iterator->getArrayCopy();
        }
        
        $Array = [];
        foreach($Iterator as $Key => $Value) {
            $Array[$Key] = $Value;
        }
        
        return $Array;
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
