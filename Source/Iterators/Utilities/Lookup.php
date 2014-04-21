<?php

namespace Pinq\Iterators\Utilities;

/**
 * Represents a  range grouped values determined from a supplied grouping function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Lookup
{
    /**
     * The dictionary containing the keyed groups
     * 
     * @var Dictionary<mixed, \ArrayObject>
     */
    private $Dictionary;
    
    private function __construct()
    {
        $this->Dictionary = new Dictionary();
    }
    
    public static function FromGroupingFunction(callable $GroupingFunction, \Traversable $Values, array &$GroupKeys = null) 
    {
        $GroupKeys = [];
        $Lookup = new self();
        
        $Values = \Pinq\Utilities::ToArray($Values);
        $GroupByValues = array_map($GroupingFunction, $Values);
        
        foreach ($GroupByValues as $ValueKey => $GroupKey) {
            
            if($Lookup->Dictionary->Contains($GroupKey)) {
                $Group = $Lookup->Dictionary->Get($GroupKey);
                $Group[$ValueKey] = $Values[$ValueKey];
            }
            else {
                $GroupKeys[] = $GroupKey;
                $Lookup->Dictionary->Set($GroupKey, new \ArrayObject([$ValueKey => $Values[$ValueKey]]));
            }
        }
        
        return $Lookup;
    }
    
    /**
     * Returns the group of values from the specified key
     * 
     * @param mixed $Key
     * @return array
     */
    public function Get($Key)
    {
        return $this->Dictionary->Get($Key)->getArrayCopy();
    }
    
    /**
     * Returns all the groups as an array
     * 
     * @return array[]
     */
    public function AsArray()
    {
        $Groups = [];
        foreach($this->Dictionary as $Key) {
            $Groups[] = $this->Dictionary->Get($Key);
        }
        
        return $Groups;
    }
    
    /**
     * Returns whether there is a specified group
     * 
     * @param mixed $Key
     * @return boolean
     */
    public function Contains($Key) 
    {
        return $this->Dictionary->Contains($Key);
    }
    
    public function getIterator()
    {
        return $this->Dictionary->getIterator();
    }
}
