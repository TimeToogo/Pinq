<?php

namespace Pinq;

class Traversable extends Queryable implements \Pinq\ITraversable, \Pinq\IOrderedTraversable
{
    public function __construct(array $Values = [])
    {
        parent::__construct(new Providers\Arrays\Provider($Values));
    }
    
    public function AsTraversable()
    {
        return $this;
    }

}
