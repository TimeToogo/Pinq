<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\ISet;
use Pinq\Iterators\Common;

/**
 * Implementation of the set using generators for iteration.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Set extends Generator implements ISet
{
    use Common\Set;
    
    public function __construct(\Traversable $values = null)
    {
        parent::__construct();
        
        if($values !== null) {
            foreach($values as $value) {
                $this->add($value);
            }
        }
    }
    
    public function getIterator()
    {
        foreach($this->values as $value) {
            yield $value;
        }
    }
}
