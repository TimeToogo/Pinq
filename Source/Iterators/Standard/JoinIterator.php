<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\Common\Joins\IInnerValuesJoiner;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinIterator extends Iterator
{
    use Common\Joins\JoinIterator;
    
    /**
     * @var int
     */
    private $count = 0;
    /**
     * @var mixed
     */
    protected $currentOuterKey;

    /**
     * @var mixed
     */
    protected $currentOuterValue;

    /**
     * @var IIterator
     */
    protected $outerIterator;

    /**
     * @var IIterator
     */
    protected $innerIterator;

    /**
     * @var IIterator
     */
    private $currentInnerGroupIterator;

    public function __construct(
            IInnerValuesJoiner $innerValuesJoiner,
            IIterator $outerIterator,
            IIterator $innerIterator,
            callable $joiningFunction)
    {
        parent::__construct();
        self::__constructIterator($innerValuesJoiner, $joiningFunction);
        $this->outerIterator = $outerIterator;
        $this->innerIterator = $innerIterator;
    }

    public function doRewind()
    {
        $this->initialize();
        $this->outerIterator->rewind();
        $this->currentInnerGroupIterator = new EmptyIterator();
        $this->count = 0;
    }
    
    protected function doFetch(&$key, &$value)
    {
        while (!$this->currentInnerGroupIterator->fetch($innerKey, $innerValue)) {
            if (!$this->outerIterator->fetch($this->currentOuterKey, $this->currentOuterValue)) {
                return false;
            }

            $this->currentInnerGroupIterator = $this->innerValuesJoiner->getInnerGroupIterator($this->currentOuterValue, $this->currentOuterKey);
            $this->currentInnerGroupIterator->rewind();
        }
        
        $joiningFunction = $this->joiningFunction;
        
        $key = $this->count++;
        $value = $joiningFunction($this->currentOuterValue, $innerValue, $this->currentOuterKey, $innerKey);
        
        return true;
    }
}
