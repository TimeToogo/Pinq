<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IOrderedMap;

/**
 * Contains the common functionality for the IOrderedMap implementation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait OrderedMap
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
    protected $length = 0;

    /**
     * @var int
     */
    protected $largestIntKey = -1;
    
    /**
     * {@inheritDoc}
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
     * @return IOrderedMap
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
        
        $map = new self();
        
        $map->keys = array_values($keys);
        $map->values = array_values($values);
        $map->length = $length;
        
        foreach($map->keys as $position => $key) {
            $map->keyIdentityPositionMap[self::identityHash($key)] = $position;
        }
        $map->loadLargestIntKey();
        
        return $map;
    }
    
    /**
     * {@inheritDoc}
     */
    public function values()
    {
        return $this->values;
    }
    
    /**
     * {@inheritDoc}
     */
    public function map(callable $function)
    {
        $clone = clone $this;
        
        foreach($clone->keyIdentityPositionMap as $position) {
            $clone->values[$position] = $function($this->values[$position], $this->keys[$position]);
        }
        
        return $clone;
    }
    
    /**
     * {@inheritDoc}
     */
    public function groupBy(callable $groupKeyFunction)
    {
        $groupedMap = new self();
        
        foreach($this->keyIdentityPositionMap as $identityHash => $position) {
            $keyCopy = $key = $this->keys[$position];
            $valueCopy = $value = $this->values[$position];
            
            $groupKey = $groupKeyFunction($valueCopy, $keyCopy);
            
            if($groupedMap->contains($groupKey)) {
                $groupMap = $groupedMap->get($groupKey);
            } else {
                $groupMap = new self();
                $groupedMap->set($groupKey, $groupMap);
            }
            
            $groupMap->setInternal($key, $value, $identityHash);
        }
        
        return $groupedMap;
    }
    
    /**
     * {@inheritDoc}
     */
    public function multisort(array $orderByFunctions, array $isAscending)
    {
        $positionKeyIdentityMap = [];
        $populatePositionMap = true;
        
        $multisortArguments = [];
        
        foreach($orderByFunctions as $key => $function) {
            $orderByValues = [];
            
            if($populatePositionMap) {
                foreach($this->keyIdentityPositionMap as $keyIdentityHash => $position) {
                    $stringPosition = '0' . $position;
                    $positionKeyIdentityMap[$stringPosition] = $keyIdentityHash;
                    $orderByValues[$stringPosition] = $function($this->values[$position], $this->keys[$position]);
                }
                $populatePositionMap = false;
            } else {
                foreach($this->keyIdentityPositionMap as $keyIdentityHash => $position) {
                    $orderByValues['0' . $position] = $function($this->values[$position], $this->keys[$position]);
                }
            }
            
            $multisortArguments[] =& $orderByValues;
            $multisortArguments[] = $isAscending[$key] ? SORT_ASC : SORT_DESC;
            $multisortArguments[] = SORT_REGULAR;
            
            unset($orderByValues);
        }
        
        $multisortArguments[] =& $positionKeyIdentityMap;
        
        call_user_func_array('array_multisort', $multisortArguments);
        
        $sortedMap = new self();
        $newPosition = 0;
        foreach ($positionKeyIdentityMap as $stringPosition => $keyIdentityHash) {
            $originalPosition = (int)$stringPosition;
            
            $sortedMap->keyIdentityPositionMap[$keyIdentityHash] = $newPosition;
            $sortedMap->keys[$newPosition] = $this->keys[$originalPosition];
            $sortedMap->values[$newPosition] = $this->values[$originalPosition];
            
            $newPosition++;
        }
        
        return $sortedMap;
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

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        $identityHash = self::identityHash($key);
        
        return isset($this->keyIdentityPositionMap[$identityHash]) ? $this->values[$this->keyIdentityPositionMap[$identityHash]] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function contains($key)
    {
        return isset($this->keyIdentityPositionMap[self::identityHash($key)]);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->setInternal($key, $value, self::identityHash($key));
    }

    /**
     * {@inheritDoc}
     */
    public function setIfNotContained($key, $value)
    {
        $identityHash = self::identityHash($key);
        
        if(!isset($this->keyIdentityPositionMap[$identityHash])) {
            $this->setInternal($key, $value, $identityHash);
            
            return true;
        }
        
        return false;
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
    
    /**
     * {@inheritDoc}
     */
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
            
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->keyIdentityPositionMap = [];
        $this->keys = [];
        $this->values = [];
        $this->length = 0;
        $this->largestIntKey = -1;
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
}
