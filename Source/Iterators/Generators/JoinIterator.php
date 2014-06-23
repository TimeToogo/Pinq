<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IJoinToIterator;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class JoinIterator extends IteratorGenerator implements IJoinToIterator
{
    use Common\JoinIterator;
    
    /**
     * @var IGenerator
     */
    protected $outerIterator;

    /**
     * @var IGenerator
     */
    protected $innerIterator;

    public function __construct(IGenerator $outerIterator, IGenerator $innerIterator)
    {
        parent::__construct($outerIterator);
        self::__constructIterator();
        $this->outerIterator =& $this->iterator;
        $this->innerIterator = $innerIterator;
    }
    
    final protected function &iteratorGenerator(IGenerator $iterator)
    {
        $generator = $this->joinGenerator($iterator, $this->innerIterator, $this->projectionFunction);
        
        foreach($generator as $key => $value) {
            yield $key => $value;
            unset($value);
        }
    }
    
    abstract protected function joinGenerator(
            IGenerator $outerIterator, 
            IGenerator $innerIterator,
            callable $projectionFunction);
}
