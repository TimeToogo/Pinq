<?php

namespace Pinq\Iterators;

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
     * @return OrderedIterator
     */
    public function AndGroupBy(callable $GroupByFunctions)
    {
        $Copy = new self($this->Iterator, 'strlen');
        
        $Copy->GroupByFunctions = $this->GroupByFunctions;
        $Copy->GroupByFunctions[] = $GroupByFunctions;
        
        return $Copy;
    }
    
    protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $Array = \Pinq\Utilities::ToArray($InnerIterator);
        
        $Groups = [];
        if(count($this->GroupByFunctions) === 1) {
            $Groups = $this->InitiliazeSingleGroupBy($this->GroupByFunctions[0], $Array);
        }
        else {
            $Groups = $this->InitiliazeCompositeGroupBy($this->GroupByFunctions, $Array);
        }
        
        return new \ArrayIterator($Groups);
    }
    
    private function InitiliazeSingleGroupBy(callable $GroupByFunction, array &$Array) 
    {
        $KeyGroupValueMap = array_map($GroupByFunction, $Array);
        
        return $this->GroupBy($KeyGroupValueMap, $Array);
    }
    
    private function InitiliazeCompositeGroupBy(array $GroupByFunctions, array &$Array) 
    {
        $KeyGroupValueMap = [];
        
        foreach ($GroupByFunctions as $GroupByKey => $GroupByFunction) {
            foreach(array_map($GroupByFunction, $Array) as $ValueKey => $GroupByValue) {
                $KeyGroupValueMap[$ValueKey][$GroupByKey] = $GroupByValue;
            }
        }
        
        return $this->GroupBy($KeyGroupValueMap, $Array);
    }
    
    private function GroupBy(array $KeyGroupValueMap, array &$ArrayToGroup) {
        $Groups = [];
        $SeenValueMap = [];
        $Count = 0;
        foreach ($KeyGroupValueMap as $ValueKey => $GroupByValue) {
            $GroupKey = array_search($GroupByValue, $SeenValueMap, true);
            
            if($GroupKey === false) {
                $GroupKey = $Count;
                $SeenValueMap[$GroupKey] = $GroupByValue;
                $Count++;
            }
            
            $Groups[$GroupKey][] = $ArrayToGroup[$ValueKey];
        }
        
        return array_map(function ($Group) { return new \Pinq\Traversable($Group); }, $Groups);
    }
}
