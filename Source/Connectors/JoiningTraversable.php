<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\IJoinIterator;

/**
 * Implements the filtering API for a join / group join traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningTraversable implements Interfaces\IJoiningOnTraversable
{
    /**
     * @var IIteratorScheme     
     */
    protected $scheme;
    
    /**
     * @var IJoinIterator
     */
    protected $joinInterator;
    
    /**
     * @var callable
     */
    protected $traversableFactory;
    
    public function __construct(IIteratorScheme $scheme, IJoinIterator $joinIterator, callable $traversableFactory)
    {
        $this->scheme = $scheme;
        $this->joinInterator = $joinIterator;
        $this->traversableFactory = $traversableFactory;
    }

    public function on(callable $joiningOnFunction)
    {
        $this->joinInterator = $this->joinInterator->filterOn($joiningOnFunction);
        
        return $this;
    }
    
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        $this->joinInterator = $this->joinInterator->filterOnEquality($outerKeyFunction, $innerKeyFunction);
        
        return $this;
    }

    public function to(callable $joinFunction)
    {
        $traversableFactory = $this->traversableFactory;
        
        return $traversableFactory($this->joinInterator->projectTo($joinFunction));
    }
}
