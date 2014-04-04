<?php

namespace Pinq\Iterators;

class ProjectionIterator extends \IteratorIterator
{
    /**
     * @var callable|null
     */
    private $KeyProjectionFunction;
    
    /**
     * @var callable|null
     */
    private $ValueProjectionFunction;
    
    public function __construct(\Traversable $Iterator, callable $KeyProjectionFunction = null, callable $ValueProjectionFunction = null)
    {
        parent::__construct($Iterator);
        $this->KeyProjectionFunction = $KeyProjectionFunction;
        $this->ValueProjectionFunction = $ValueProjectionFunction;
    }
    
    public function key()
    {
        $Function = $this->KeyProjectionFunction;
        return $Function === null ? parent::key() : $Function(parent::current());
    }
    
    public function current()
    {
        $Function = $this->ValueProjectionFunction;
        return $Function === null ? parent::current() : $Function(parent::current());
    }
}
