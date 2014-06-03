<?php

namespace Pinq\Iterators;

/**
 * Base class for a join iterator with a custom join on function.
 * The equi-join lookup optimization cannot be made and the function must be run
 * for every outer/inner pair.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class CustomJoinIteratorBase extends JoinIteratorBase
{
    /**
     * @var callable
     */
    protected $joinOnFunction;

    /**
     * @var Utilities\OrderedMap
     */
    protected $innerValues;

    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $joinOnFunction, callable $joiningFunction)
    {
        parent::__construct($outerIterator, $innerIterator, $joiningFunction);
        $this->joinOnFunction = Utilities\Functions::allowExcessiveArguments($joinOnFunction);
    }

    final protected function initialize()
    {
        $this->innerValues = new Utilities\OrderedMap($this->innerIterator);
    }

    final protected function getInnerGroupIterator($outerValue, $outerKey)
    {
        $joinOnFunction = $this->joinOnFunction;
        $innerValueFilterFunction = function ($innerValue, $innerKey) use ($outerValue, $outerKey, $joinOnFunction) {
            return $joinOnFunction($outerValue, $innerValue, $outerKey, $innerKey);
        };

        return $this->getInnerGroupValuesIterator($innerValueFilterFunction);
    }

    abstract protected function getInnerGroupValuesIterator(callable $innerValueFilterFunction);
}
