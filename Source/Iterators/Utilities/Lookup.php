<?php

namespace Pinq\Iterators\Utilities;

class Lookup
{
    /**
     * @var Dictionary
     */
    private $Dictionary;
    
    public function __construct()
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
     * @return array
     */
    public function Get($Key)
    {
        return $this->Dictionary->Get($Key)->getArrayCopy();
    }
    
    /**
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
    
    public function Contains($Key) 
    {
        return $this->Dictionary->Contains($Key);
    }
    
    public function getIterator()
    {
        return $this->Dictionary->getIterator();
    }
}
