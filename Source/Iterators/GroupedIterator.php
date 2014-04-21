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
    private $GroupByFunctions = [];
    
    public function __construct(\Traversable $Iterator, callable $GroupByFunction)
    {
        parent::__construct($Iterator);
        $this->GroupByFunctions[] = $GroupByFunction;
    }
    
    /**
     * @return GroupedIterator
     */
    public function AndGroupBy(callable $GroupByFunctions)
    {
        $Copy = new self($this->Iterator, $GroupByFunctions);
        
        $Copy->GroupByFunctions = $this->GroupByFunctions;
        $Copy->GroupByFunctions[] = $GroupByFunctions;
        
        return $Copy;
    }
    
    protected function InitializeIterator(\Traversable $InnerIterator)
    {   
        if(count($this->GroupByFunctions) === 1) {
            $GroupByFunction = $this->GroupByFunctions[0];
        }
        else {
            $GroupByFunction = function ($Value) {
                return array_map(function ($I) use ($Value) { return $I($Value); }, $this->GroupByFunctions);
            };
        }
        
        $GroupKeys = [];
        $GroupLookup = Utilities\Lookup::FromGroupingFunction($GroupByFunction, $InnerIterator, $GroupKeys);
        
        $Groups = [];
        foreach ($GroupKeys as $GroupKey) {
            $Groups[] = new \Pinq\Traversable($GroupLookup->Get($GroupKey));
        }
        
        return new \ArrayIterator($Groups);
    }
}
