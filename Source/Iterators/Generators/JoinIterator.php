<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\Common\Joins\IInnerValuesJoiner;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinIterator extends Generator
{
    use Common\Joins\JoinIterator;
    
    /**
     * @var \Traversable
     */
    protected $outerIterator;

    /**
     * @var \Traversable
     */
    protected $innerIterator;

    public function __construct(IInnerValuesJoiner $innerValuesJoiner, \Traversable $outerIterator, \Traversable $innerIterator, callable $joiningFunction)
    {
        parent::__construct();
        self::__constructIterator($innerValuesJoiner, $joiningFunction);
        $this->outerIterator = $outerIterator;
        $this->innerIterator = $innerIterator;
    }
    
    public function getIterator()
    {
        $this->initialize();
        
        $joiningFunction = $this->joiningFunction;
        
        foreach($this->outerIterator as $outerKey => $outerValue) {
            foreach($this->innerValuesJoiner->getInnerGroupIterator($outerValue, $outerKey) as $innerKey => $innerValue) {
                yield $joiningFunction($outerValue, $innerValue, $outerKey, $innerKey);
            }
        }
    }
}
