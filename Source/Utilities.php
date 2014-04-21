<?php

namespace Pinq;

DEFINE('IS_PHP_55', version_compare(PHP_VERSION, '5.5', '>='));

/**
 * General utility class providing common and misc behaviour
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Utilities
{
    private function __construct() { }
    
    /**
     * Returns the type or class of the supplied function
     * 
     * @param mixed $Value The value
     * @return string The type or class
     */
    public static function GetTypeOrClass($Value)
    {
        return is_object($Value) ? get_class($Value) : gettype($Value);
    }
    
    /**
     * Returns whether the value is iterable
     * 
     * @param mixed $Value The value
     * @return boolean Whether the value is iterable
     */
    public static function IsIterable($Value)
    {
        return $Value instanceof \Traversable || is_array($Value);
    }
    
    /**
     * Returns the iterator as an array
     * 
     * @param \Traversable $Iterator The iterator value
     * @return array
     */
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
            $Iterator = self::ToIterator($Iterator);
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
     * Returns the values as an iterator
     * 
     * @param array|\Traversable $TraversableOrArray The value
     * @return \Iterator
     * @throws PinqException If the value is not a array nor \Traversable
     */
    public static function ToIterator($TraversableOrArray)
    {
        if(!self::IsIterable($TraversableOrArray)) {
            throw PinqException::InvalidIterable(__METHOD__, $TraversableOrArray);
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
    
    /**
     * Returns whether the supplied name is cosidered normal name syntax
     * and can be used plainly in code.
     * 
     * Example:
     * 'foo' -> yes: $foo
     * 'foo bar' -> no: ${'foo bar'}
     * 
     * @param string $Name The field, function, method or variable name
     * @return boolean
     */
    public static function IsNormalSyntaxName($Name) 
    {
        return (bool)preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $Name);
    }
}
