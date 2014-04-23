<?php 

namespace Pinq\Iterators;

/**
 * Groups the values from the supplied grouping function(s)
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupedIterator extends LazyIterator
{
    /**
     * @var callable[]
     */
    private $groupByFunctions = [];
    
    public function __construct(\Traversable $iterator, callable $groupByFunction)
    {
        parent::__construct($iterator);
        $this->groupByFunctions[] = $groupByFunction;
    }
    
    /**
     * @return GroupedIterator
     */
    public function andGroupBy(callable $groupByFunctions)
    {
        $copy = new self($this->iterator, $groupByFunctions);
        $copy->groupByFunctions = $this->groupByFunctions;
        $copy->groupByFunctions[] = $groupByFunctions;
        
        return $copy;
    }
    
    protected function initializeIterator(\Traversable $innerIterator)
    {
        if (count($this->groupByFunctions) === 1) {
            $groupByFunction = $this->groupByFunctions[0];
        }
        else {
            $groupByFunction = function ($value) {
                return array_map(function ($i) use($value) {
                    return $i($value);
                }, $this->groupByFunctions);
            };
        }
        
        $groupKeys = [];
        $groupLookup = 
                Utilities\Lookup::fromGroupingFunction(
                        $groupByFunction,
                        $innerIterator,
                        $groupKeys);
        $groups = [];
        
        foreach ($groupKeys as $groupKey) {
            $groups[] = new \Pinq\Traversable($groupLookup->get($groupKey));
        }
        
        return new \ArrayIterator($groups);
    }
}