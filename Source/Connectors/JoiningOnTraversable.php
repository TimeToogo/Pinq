<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Iterators\IIteratorScheme;

/**
 * Implements the filtering API for a join / group join traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningOnTraversable implements Interfaces\IJoiningOnTraversable, Interfaces\IJoiningOnCollection
{
    /**
     * @var IIteratorScheme     
     */
    private $scheme;
    
    /**
     * @var \Traversable
     */
    private $outerValues;

    /**
     * @var \Traversable
     */
    private $innerValues;

    /**
     * @var boolean
     */
    private $isGroupJoin;


    /**
     * @var callable
     */
    private $traversableFactory;

    /**
     * @param boolean $isGroupJoin
     */
    public function __construct(IIteratorScheme $scheme, \Traversable $outerValues, \Traversable $innerValues, $isGroupJoin, callable $traversableFactory)
    {
        $this->scheme = $scheme;
        $this->outerValues = $outerValues;
        $this->innerValues = $innerValues;
        $this->isGroupJoin = $isGroupJoin;
        $this->traversableFactory = $traversableFactory;
    }

    public function on(callable $joiningOnFunction)
    {
        return new JoiningToConnector($this->onIteratorFactory($joiningOnFunction));
    }
    
    final protected function onIteratorFactory(callable $joiningOnFunction)
    {
        return function (callable $joiningFunction) use ($joiningOnFunction) {
            if ($this->isGroupJoin) {
                $iterator = $this->scheme->customGroupJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $joiningOnFunction,
                        $joiningFunction,
                        $this->traversableFactory);
            } else {
                $iterator = $this->scheme->customJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $joiningOnFunction,
                        $joiningFunction);
            }
            
            $traversableFactory = $this->traversableFactory;
            return $traversableFactory($iterator);
        };
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new JoiningToConnector($this->onEqualityTravserableFactory($outerKeyFunction, $innerKeyFunction));
    }
    
    final protected function onEqualityTravserableFactory(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return function (callable $joiningFunction) use ($outerKeyFunction, $innerKeyFunction) {
            if ($this->isGroupJoin) {
                $iterator = $this->scheme->equalityGroupJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $outerKeyFunction,
                        $innerKeyFunction,
                        $joiningFunction,
                        $this->traversableFactory);
            } else {
                $iterator = $this->scheme->equalityJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $outerKeyFunction,
                        $innerKeyFunction,
                        $joiningFunction);
            }
            
            $traversableFactory = $this->traversableFactory;
            return $traversableFactory($iterator);
        };
    }
}
