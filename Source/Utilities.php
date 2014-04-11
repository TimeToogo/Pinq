<?php

namespace Pinq;

DEFINE('IS_PHP_55', version_compare(PHP_VERSION, '5.5', '>='));

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

    public static function IsTransitive(array $Array)
    {
        foreach($Array as $Value) {
            if(is_string($Value) || is_integer($Value)) {
                continue;
            }
            return false;
        }
        
        return true;
    }
    
    public static function GetTypeOrClass($Value)
    {
        return is_object($Value) ? get_class($Value) : gettype($Value);
    }
    
    public static function ToArray(\Traversable $Iterator)
    {
        if($Iterator instanceof \ArrayIterator || $Iterator instanceof \ArrayObject) {
            return $Iterator->getArrayCopy();
        }
        
        $Array = [];
        
        if(IS_PHP_55) {
            foreach($Iterator as $Key => $Value) {
                $Array[$Key] = $Value;
            }
        }
        else {
            //Does not support returning custom keys
            $Iterator->rewind();
            while ($Iterator->valid()) {
                $Array[$Iterator->key()] = $Iterator->current();
                $Iterator->next();
            }
        }
        
        return $Array;
    }
    
    /**
     * @return \Iterator
     */
    public static function ToIterator($TraversableOrArray)
    {
        if(!is_array($TraversableOrArray) && !($TraversableOrArray instanceof \Traversable)) {
            throw new PinqException(
                    'Invalid argument for %s: expecting array or \Traversable, %s given',
                    __METHOD__,
                    self::GetTypeOrClass($TraversableOrArray));
        }
        
        if($TraversableOrArray instanceof \Iterator) {
            return $TraversableOrArray;
        }
        else if($TraversableOrArray instanceof \IteratorAggregate) {
            return $TraversableOrArray->getIterator();
        }
        else if($TraversableOrArray instanceof \Traversable) {
            return new \IteratorIterator($TraversableOrArray);
        }
        else {
            return new \ArrayIterator($TraversableOrArray);
        }
    }
    
    public static function MultisortPreserveKeys(array $OrderArguments, array &$ArrayToSort)
    {
        $StringKeysArray = [];
        foreach ($ArrayToSort as $Key => $Value) {
            $StringKeysArray['a' . $Key] = $Value;
        }
        
        if(!defined('HHVM_VERSION')) {
            $OrderArguments[] =& $StringKeysArray;
            call_user_func_array('array_multisort', $OrderArguments);
        }
        else {
            //HHVM Compatibility: hhvm array_multisort wants all argument by ref?
            $ReferencedOrderArguments = [];
            foreach($OrderArguments as $Key => $OrderArgument) {
                $ReferencedOrderArguments[] =& $OrderArgument;
                unset($OrderArgument);
            }
            $ReferencedOrderArguments[] =& $StringKeysArray;
            
            call_user_func_array('array_multisort', $ReferencedOrderArguments);
        }
        
        $UnserializedKeyArray = [];
        foreach ($StringKeysArray as $Key => $Value) {
            $UnserializedKeyArray[substr($Key, 1)] = $Value;
        }
        
        $ArrayToSort = $UnserializedKeyArray;
    }
}
