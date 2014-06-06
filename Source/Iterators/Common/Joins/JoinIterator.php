<?php

namespace Pinq\Iterators\Common\Joins;

use Pinq\Iterators\Common;

/**
 * Common functionality for a join iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait JoinIterator
{
    /**
     * @var boolean
     */
    protected $isInitialized = false;
    
    /**
     * @var IInnerValuesJoiner
     */
    protected $innerValuesJoiner;

    /**
     * @var callable
     */
    protected $joiningFunction;
    
    final protected function __constructIterator(IInnerValuesJoiner $innerValuesJoiner, callable $joiningFunction)
    {
        $this->innerValuesJoiner = $innerValuesJoiner;
        $this->joiningFunction = Common\Functions::allowExcessiveArguments($joiningFunction);
    }
    
    final protected function initialize(\Traversable $innerIterator)
    {
        if (!$this->isInitialized) {
            $this->innerValuesJoiner->initialize($innerIterator);
            $this->isInitialized = true;
        }
    }
}
