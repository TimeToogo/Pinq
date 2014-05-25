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
    
    /**
     * @var callable
     */
    private $traversableFactory;

    public function __construct(\Traversable $iterator, callable $groupByFunction, callable $traversableFactory = null)
    {
        parent::__construct($iterator);
        $this->groupByFunctions[] = Utilities\Functions::allowExcessiveArguments($groupByFunction);
        $this->traversableFactory = $traversableFactory ?: \Pinq\Traversable::factory();
    }

    /**
     * @return GroupedIterator
     */
    public function andGroupBy(callable $groupByFunctions)
    {
        $copy = new self($this->iterator, $groupByFunctions);
        $copy->groupByFunctions = $this->groupByFunctions;
        $copy->groupByFunctions[] = Utilities\Functions::allowExcessiveArguments($groupByFunctions);

        return $copy;
    }

    protected function initializeIterator(\Traversable $innerIterator)
    {
        if (count($this->groupByFunctions) === 1) {
            $groupByFunction = $this->groupByFunctions[0];
        } else {
            $groupByFunctions = $this->groupByFunctions;
            $groupByFunction = function ($value, $key) use ($groupByFunctions) {
                $groupByValue = [];
                foreach($groupByFunctions as $key => $groupByFunction) {
                    $groupByValue[$key] = $groupByFunction($value, $key);
                }
                
                return $groupByValue;
            };
        }

        $groupKeys = [];
        $groupLookup =
                Utilities\Lookup::fromGroupingFunction(
                        $groupByFunction,
                        $innerIterator,
                        $groupKeys);
        
        $groups = [];
        
        $traversableFactory = $this->traversableFactory;
        foreach ($groupKeys as $groupKey) {
            $groups[] = $traversableFactory($groupLookup->get($groupKey));
        }

        return new \ArrayIterator($groups);
    }
}
