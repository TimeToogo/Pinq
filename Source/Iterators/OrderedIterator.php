<?php

namespace Pinq\Iterators;

/**
 * Orders the values according to the supplied functions and directions
 * using array_multisort
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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

        self::MultisortPreserveKeys($MultisortArguments, $Array);

        return new \ArrayIterator($Array);
    }
    
    private static function MultisortPreserveKeys(array $OrderArguments, array &$ArrayToSort)
    {
        $StringKeysArray = [];
        foreach ($ArrayToSort as $Key => $Value) {
            $StringKeysArray['a' . $Key] = $Value;
        }
        
        if(!defined('HHVM_VERSION')) {
            $OrderArguments[] =& $StringKeysArray;
            call_user_func_array('array_multisort', $OrderArguments);
        }
        else {
            //HHVM Compatibility: hhvm array_multisort wants all argument by ref?
            $ReferencedOrderArguments = [];
            foreach($OrderArguments as $Key => &$OrderArgument) {
                $ReferencedOrderArguments[$Key] =& $OrderArgument;
            }
            $ReferencedOrderArguments[] =& $StringKeysArray;
            
            call_user_func_array('array_multisort', $ReferencedOrderArguments);
        }
        
        $UnserializedKeyArray = [];
        foreach ($StringKeysArray as $Key => $Value) {
            $UnserializedKeyArray[substr($Key, 1)] = $Value;
        }
        
        $ArrayToSort = $UnserializedKeyArray;
    }
}
