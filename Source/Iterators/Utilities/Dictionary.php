<?php

namespace Pinq\Iterators\Utilities;

/**
 * This class is like an array that supports keys of any type: Optimized for quick lookup speeds.
 * Depending on the key type, the key value pair is stored in the most optimized way to check if 
 * they are contained in the dictionary.
 * 
 * Key type     - Value storage method:
 * 
 * string       - array value indexed by key
 * integer      - array value indexed by key
 * double       - array value indexed by string cast key
 * boolean      - array value indexed by key (auto casts to int 1/0)
 * object       - info in \SplObjectStorage
 * null         - Single value (only one null)
 * array        - array tuple (no good way to hash an array identity)
 * resource     - array tuple with string cast array key (will become 'Resource id #{id}', guaranteed unique as per docs)
 * unknown type - array tuple (wtf, why does this exist)
 */
class Dictionary implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $Storage;
    
    public function __construct()
    {
        $this->Storage = [
            'array' => [],
            'boolean' => [],
            'integer' => [],
            'double' => [],
            'string' => [],
            'object' => new \SplObjectStorage(),
            'resource' => [],
            'unknown type' => [],
        ];
    }
    
    public function Get($Key)
    {
        if($Key === null) {
            return $this->Storage['NULL'];
        }
        
        $Type = gettype($Key);
        $TypeStorage =& $this->Storage[$Type];
        switch ($Type) {
            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                return $TypeStorage[$Key];
                
            case 'double':
                return $TypeStorage[(string)$Key];
                
            case 'resource':
                return $TypeStorage[(string)$Key][1];
            
            case 'array':
            case 'unknown type':
                foreach ($TypeStorage as $KeyValuePair) {
                    if($KeyValuePair[0] === $Key) {
                        return $KeyValuePair[1];
                    }
                }
                return null;
        }
    }
    
    public function Contains($Key) 
    {
        if($Key === null) {
            return array_key_exists('NULL', $this->Storage);
        }
        
        $Type = gettype($Key);
        $TypeStorage =& $this->Storage[$Type];
        switch ($Type) {
            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                return isset($TypeStorage[$Key]);
                
            case 'double':
            case 'resource':
                return isset($TypeStorage[(string)$Key]);
            
            case 'array':
            case 'unknown type':
                foreach ($TypeStorage as $KeyValuePair) {
                    if($KeyValuePair[0] === $Key) {
                        return true;
                    }
                }
                return false;
        }
    }

    public function Set($Key, $Value) 
    {
        $Type = gettype($Key);
        $TypeStorage =& $this->Storage[$Type];
        switch ($Type) {
            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                $TypeStorage[$Key] = $Value;
                break;
            
            case 'double':
                $TypeStorage[(string)$Key] = $Value;
                break;
            
            case 'NULL':
                $TypeStorage = $Value;
                break;
                
            case 'resource':
                $TypeStorage[(string)$Key] = [$Key, $Value];
                break;
            
            case 'array':
            case 'unknown type':
                $TypeStorage[] = [$Key, $Value];
                break;
        }
    }
    
    public function AddRange($Values) 
    {
        foreach ($Values as $Key => $Value) {
            $this->Set($Key, $Value);
        }
    }
    
    public function Remove($Key) 
    {
        $Type = gettype($Key);
        $TypeStorage =& $this->Storage[$Type];
        switch ($Type) {
            
            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                unset($TypeStorage[$Key]);
                break;
            
            case 'double':
            case 'resource':
                unset($TypeStorage[(string)$Key]);
                break;
            
            case 'NULL':
                unset($this->Storage['NULL']);
                break;
                
            case 'array':
            case 'unknown type':
                foreach ($TypeStorage as $StorageKey => $KeyValuePair) {
                    if($KeyValuePair[0] === $Key) {
                        unset($TypeStorage[$StorageKey]);
                        break;
                    }
                }
                break;
        }
    }
    
    public function RemoveRange($Keys) 
    {
        foreach ($Keys as $Key) {
            $this->Remove($Key);
        }
    }
    
    /**
     * This will return an iterator for all the keys in the dictionary.
     * ->Get($Key) can be called to retrieve the value.
     * This is due to PHP limiting the types of values allowed to be returned
     * as an iterator key.
     * 
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_values(array_merge(
                array_map('strval', array_keys($this->Storage['string'])),
                array_keys($this->Storage['integer']),
                array_map('boolval', array_keys($this->Storage['boolean'])),
                array_map('doubleval', array_keys($this->Storage['double'])),
                iterator_to_array($this->Storage['object'], false),
                array_key_exists('NULL', $this->Storage) ? [null] : [],
                array_map('reset', $this->Storage['resource']),
                array_map('reset', $this->Storage['array']),
                array_map('reset', $this->Storage['unknown type'])
            )));
    }
}
