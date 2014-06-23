<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\IJoinIterator;

/**
 * Implements the filtering API for a join / group join collection.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningCollection extends JoiningTraversable implements Interfaces\IJoiningOnCollection
{
    /**
     * @var \Pinq\ICollection 
     */
    private $collection;
    
    public function __construct(\Pinq\ICollection $collection, IJoinIterator $joinIterator, callable $collectionFactory)
    {
        parent::__construct($collection->getIteratorScheme(), $joinIterator, $collectionFactory);
        $this->collection = $collection;
    }
    
    public function apply(callable $applyFunction)
    {
        if($applyFunction instanceof \Pinq\FunctionExpressionTree) {
            $applyFunction = $applyFunction->getCompiledFunction();
        }
        
        $this->joinInterator->walk($applyFunction);
    }
}
