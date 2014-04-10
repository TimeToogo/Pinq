<?php

namespace Pinq\Iterators;

/**
 * Optimized for quick lookup speeds, depending on their type, values are stored in
 * the most optimized way to check if they are contained in the set.
 * 
 * This is designed to check via strict equality (===)
 * 
 * Type storage methods:
 * string       - array key
 * integer      - array key
 * double       - string cast array key
 * boolean      - array key (auto casts to int 1/0)
 * object       - SplObjectStorage (hash set for objects)
 * null         - boolean (their is either a null value or not)
 * array        - array value (no good way to hash an array identity)
 * resource     - string cast array key (will become 'Resource id #{id}', guaranteed unique as per docs)
 * unknown type - array value (wtf, why does this exist)
 */
class HashSet implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $Storage = [];
    
    public function __construct($Values = null)
    {
        $this->Storage = [
            'array' => [],
            'boolean' => [],
            'integer' => [],
            'double' => [],
            'string' => [],
            'object' => new \SplObjectStorage(),
            'NULL' => false,
            'resource' => [],
            'unknown type' => [],
        ];
        
        if($Values !== null) {
            $this->AddRange($Values);
        }
    }
    
    public function Contains($Value) 
    {
        $Type = gettype($Value);
        switch ($Type) {
            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                return isset($this->Storage[$Type][$Value]);
                
            case 'double':
            case 'resource':
                return isset($this->Storage[$Type][(string)$Value]);
            
            case 'NULL':
                return $this->Storage['NULL'];
            
            case 'array':
            case 'unknown type':
                return in_array($Value, $this->Storage[$Type], true);
        }
    }

    public function Add($Value) 
    {
        if($this->Contains($Value)) {
            return false;
        }
        $Type = gettype($Value);
        switch ($Type) {
            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                $this->Storage[$Type][$Value] = true;
                break;
            
            case 'double':
            case 'resource':
                $this->Storage[$Type][(string)$Value] = true;
                break;
            
            case 'NULL':
                $this->Storage['NULL'] = true;
                break;
                
            case 'array':
            case 'unknown type':
                $this->Storage[$Type][] = $Value;
                break;
        }
        
        return true;
    }
    
    public function AddRange($Values) 
    {
        foreach ($Values as $Value) {
            $this->Add($Value);
        }
    }
    
    public function Remove($Value) 
    {
        if(!$this->Contains($Value)) {
            return false;
        }
        $Type = gettype($Value);
        switch ($Type) {
            
            case 'string':
            case 'integer':
            case 'boolean':
                unset($this->Storage[$Type][$Value]);
                break;
            
            case 'object':
                unset($this->Storage[$Type][$Value]);
                break;
            
            case 'double':
            case 'resource':
                unset($this->Storage[$Type][(string)$Value]);
                break;
            
            case 'NULL':
                $this->Storage['NULL'] = false;
                break;
                
            case 'array':
            case 'unknown type':
                unset($this->Storage[$Type][array_search($Value, $this->Storage[$Type], true)]);
        }
        
        return true;
    }
    
    public function RemoveRange($Values) 
    {
        foreach ($Values as $Value) {
            $this->Remove($Value);
        }
    }
    
    public function getIterator()
    {
        return new \ArrayIterator(array_values(array_merge(
                $this->Storage['string'], 
                $this->Storage['integer'],
                $this->Storage['boolean'],
                iterator_to_array($this->Storage['object'], false),
                $this->Storage['NULL'] ? [null] : [],
                $this->Storage['resource'],
                $this->Storage['array'],
                $this->Storage['unknown type'])));
    }
}

?>
