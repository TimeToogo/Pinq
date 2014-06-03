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
    private $orderByFunctions = [];

    /**
     * @var boolean[]
     */
    private $isAscendingArray = [];

    public function __construct(\Traversable $iterator, callable $orderByFunction, $isAscending)
    {
        parent::__construct($iterator);
        $this->orderByFunctions[] = Utilities\Functions::allowExcessiveArguments($orderByFunction);
        $this->isAscendingArray[] = $isAscending;
    }

    /**
     * @param boolean $isAscending
     * @return OrderedIterator
     */
    public function thenOrderBy(callable $orderByFunction, $isAscending)
    {
        $copy = new self($this->iterator, $orderByFunction, $isAscending);
        $copy->orderByFunctions = $this->orderByFunctions;
        $copy->isAscendingArray = $this->isAscendingArray;
        $copy->orderByFunctions[] = Utilities\Functions::allowExcessiveArguments($orderByFunction);
        $copy->isAscendingArray[] = $isAscending;

        return $copy;
    }

    protected function initializeIterator(\Traversable $innerIterator)
    {
        $map = new Utilities\OrderedMap($innerIterator);
        
        $keys = $map->keys();
        $values = $map->values();
        
        $multisortArguments = [];

        foreach ($this->orderByFunctions as $functionKey => $orderFunction) {
            $orderColumnValues = $map->mapToArray($orderFunction);
            
            $multisortArguments[] =& $orderColumnValues;
            $multisortArguments[] = $this->isAscendingArray[$functionKey] ? SORT_ASC : SORT_DESC;
            $multisortArguments[] = SORT_REGULAR;
            unset($orderColumnValues);
        }

        $orderedValues = self::multisortPreserveKeys($multisortArguments, $values);
        $orderedKeys = [];
        foreach($orderedValues as $position => $value) {
            $orderedKeys[$position] = $keys[$position];
        }

        return Utilities\OrderedMap::from($orderedKeys, $orderedValues);
    }

    private static function multisortPreserveKeys(array $orderArguments, array &$arrayToSort)
    {
        $stringKeysArray = [];

        foreach ($arrayToSort as $key => $value) {
            $stringKeysArray['a' . $key] = $value;
        }

        if (!defined('HHVM_VERSION')) {
            $orderArguments[] =& $stringKeysArray;
            call_user_func_array('array_multisort', $orderArguments);
        } else {
            //HHVM Compatibility: hhvm array_multisort wants all argument by ref?
            $referencedOrderArguments = [];

            foreach ($orderArguments as $key => &$orderArgument) {
                $referencedOrderArguments[$key] =& $orderArgument;
            }

            $referencedOrderArguments[] =& $stringKeysArray;
            call_user_func_array('array_multisort', $referencedOrderArguments);
        }

        $unserializedKeyArray = [];

        foreach ($stringKeysArray as $key => $value) {
            $unserializedKeyArray[substr($key, 1)] = $value;
        }

        return $unserializedKeyArray;
    }
}
