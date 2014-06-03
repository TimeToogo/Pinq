<?php

namespace Pinq\Iterators\Utilities;

/**
 * This class acts like an array but supports keys of all types.
 * 
 * This is class is not array compatible as it returns non scalar
 * values for Iterator::key and therefore should not be foreach'd with keys
 * prior to PHP 5.5
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderedMap extends \Pinq\Iterators\Iterator implements \Pinq\IIterator, \ArrayAccess, \Countable
{
    /**
     * @var array
     */
    protected $keys = [];
    
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $keyIdentityPositionMap = [];

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $length = 0;

    /**
     * @var int
     */
    protected $largestIntKey = -1;

    public function __construct($values = null)
    {
        if($values !== null) {
            $iterator = \Pinq\Utilities::toIterator($values);

            $iterator->rewind();
            while($iterator->fetch($key, $value)) {
                $this->set($key, $value);
            }
        }
    }
    
    /**
     * Returns all the keys from the dictionary as an array.
     *
     * @return array
     */
    public function keys()
    {
        return $this->keys;
    }
    
    /**
     * Constructs an ordered dictionary from an array of keys and of values.
     * The values of each array will be used and are associated by the order
     * in the supplied array.
     * 
     * @return OrderedMap
     */
    public static function from(array $keys, array $values)
    {
        $length = count($keys);
        if($length !== count($values)) {
            throw new \Pinq\PinqException(
                    'Cannot construct %s: $values and $keys must contain an equal number of elements, got %d keys and %d values',
                    __CLASS__,
                    $length,
                    count($values));
        }
        
        $dictionary = new self();
        
        $dictionary->keys = array_values($keys);
        $dictionary->values = array_values($values);
        $dictionary->length = $length;
        
        foreach($dictionary->keys as $position => $key) {
            $dictionary->keyIdentityPositionMap[self::identityHash($key)] = $position;
        }
        $dictionary->loadLargestIntKey();
        
        return $dictionary;
    }
    
    /**
     * Returns all the values from the dictionary as an array.
     *
     * @return array
     */
    public function values()
    {
        return $this->values;
    }
    
    /**
     * Maps the keys / values of the dictionary to an array.
     *
     * @return array
     */
    public function mapToArray(callable $function)
    {
        $array = [];
        
        foreach($this->keyIdentityPositionMap as $position) {
            $array[$position] = $function($this->values[$position], $this->keys[$position]);
        }
        
        return $array;
    }
    
    /**
     * Maps the keys / values of the dictionary to new dictionary.
     *
     * @return OrderedMap
     */
    public function map(callable $function)
    {
        $clone = clone $this;
        
        foreach($clone->keyIdentityPositionMap as $position) {
            $clone->values[$position] = $function($this->values[$position], $this->keys[$position]);
        }
        
        return $clone;
    }
    
    public function count()
    {
        return $this->length;
    }
    
    private static function identityHash($value)
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
                return self::arrayIdentityHash($value);
        }
    }
    
    protected static function arrayIdentityHash(array $array)
    {
        array_walk_recursive($array, function (&$value) {
            $value = self::identityHash($value);
        });
        
        $hashData = serialize($array);
        return 'a' . (strlen($hashData) > 32 ? md5($hashData) : $hashData);
    }
    
    private function loadLargestIntKey()
    {
        $this->largestIntKey = -1;
        foreach($this->keys as $key) {
            if(is_int($key) && $key > $this->largestIntKey) {
                $this->largestIntKey = $key;
            }
        }
    }

    public function get($key)
    {
        $identityHash = self::identityHash($key);
        
        return isset($this->keyIdentityPositionMap[$identityHash]) ? $this->values[$this->keyIdentityPositionMap[$identityHash]] : null;
    }

    public function contains($key)
    {
        return isset($this->keyIdentityPositionMap[self::identityHash($key)]);
    }

    public function set($key, $value)
    {
        $this->setInternal($key, $value, self::identityHash($key));
    }
    
    final protected function setInternal($key, $value, $identityHash)
    {
        if(isset($this->keyIdentityPositionMap[$identityHash])) {
            $position = $this->keyIdentityPositionMap[$identityHash];
        } else {
            $position = $this->length++;
            $this->keyIdentityPositionMap[$identityHash] = $position;
        }
        if(is_int($key) && $key > $this->largestIntKey) {
            $this->largestIntKey = $key;
        }
        
        $this->keys[$position] = $key;
        $this->values[$position] = $value;
    }
    
    public function remove($key)
    {
        $identityHash = self::identityHash($key);
        
        if(isset($this->keyIdentityPositionMap[$identityHash])) {
            $position = $this->keyIdentityPositionMap[$identityHash];
            
            unset($this->keys[$position], $this->values[$position], $this->keyIdentityPositionMap[$identityHash]);
            
            if($position !== $this->length) {
                $this->keys = array_values($this->keys);
                $this->values = array_values($this->values);
            }
            $this->length--;
            
            if($key === $this->largestIntKey) {
                $this->loadLargestIntKey();
            }
        }
    }
    
    public function offsetExists($offset)
    {
        return $this->contains($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        if($offset === null) {
            $offset = ++$this->largestIntKey;
        }
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }
    
    protected function doFetch(&$key, &$value)
    {
        if(isset($this->keys[$this->position]) || array_key_exists($this->position, $this->keys)) {
            $key = $this->keys[$this->position];
            $value = $this->values[$this->position];
            $this->position++;
            
            return true;
        }
        
        return false;
    }

    public function doRewind()
    {
        $this->position = 0;
    }
}
