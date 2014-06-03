<?php

namespace Pinq\Iterators\Utilities;

/**
 * Represents a  range grouped values determined from a supplied grouping function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupedMap extends OrderedMap
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Constructs a lookup from the the supplied iterator, grouped by the supplied function.
     * 
     * @param callable $groupingFunction
     * @param \Traversable $iterator
     * @return GroupedMap
     */
    public static function fromFunction(callable $groupingFunction, \Traversable $iterator)
    {
        $map = new OrderedMap($iterator);
        $groupedMap = new self();
        
        foreach($map->keyIdentityPositionMap as $identityHash => $position) {
            $key = $map->keys[$position];
            $value = $map->values[$position];
            
            $groupKey = $groupingFunction($value, $key);
            
            if($groupedMap->contains($groupKey)) {
                $groupDictionary = $groupedMap->get($groupKey);
            } else {
                $groupDictionary = new OrderedMap();
                $groupedMap->set($groupKey, $groupDictionary);
            }
            
            $groupDictionary->setInternal($key, $value, $identityHash);
        }
        
        return $groupedMap;
    }
}
