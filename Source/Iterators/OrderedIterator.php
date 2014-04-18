<?php

namespace Pinq\Iterators;

class OrderedIterator extends LazyIterator
{
    /**
     * @var callable[]
     */
    private $OrderByFunctions = [];
    
    /**
     * @var boolean[]
     */
    private $IsAscendingArray = [];
    
    public function __construct(\Traversable $Iterator, callable $OrderByFunction, $IsAscending)
    {
        parent::__construct($Iterator);
        $this->OrderByFunctions[] = $OrderByFunction;
        $this->IsAscendingArray[] = $IsAscending;
    }
    
    /**
     * @param boolean $IsAscending
     * @return OrderedIterator
     */
    public function ThenOrderBy(callable $OrderByFunction, $IsAscending)
    {
        $Copy = new self($this->Iterator, $OrderByFunction, $IsAscending);
        
        $Copy->OrderByFunctions = $this->OrderByFunctions;
        $Copy->IsAscendingArray = $this->IsAscendingArray;
        
        $Copy->OrderByFunctions[] = $OrderByFunction;
        $Copy->IsAscendingArray[] = $IsAscending;
        
        return $Copy;
    }
    
    protected function InitializeIterator(\Traversable $InnerIterator)
    {
        $Array = \Pinq\Utilities::ToArray($InnerIterator);
        
        $MultisortArguments = [];
        foreach ($this->OrderByFunctions as $Key => $OrderFunction) {
            $OrderColumnValues = array_map($OrderFunction, $Array);

            $MultisortArguments[] =& $OrderColumnValues;
            $MultisortArguments[] = $this->IsAscendingArray[$Key] ? SORT_ASC : SORT_DESC;
            $MultisortArguments[] = SORT_REGULAR;
            
            unset($OrderColumnValues);
        }

        \Pinq\Utilities::MultisortPreserveKeys($MultisortArguments, $Array);

        return new \ArrayIterator($Array);
    }
}
