<?php

namespace Pinq\Analysis\TypeOperations;

use Pinq\Analysis\IConstructor;
use Pinq\Analysis\ITypeSystem;

/**
 * Implementation of a constructor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Constructor extends TypeOperation implements IConstructor
{
    /**
     * @var \ReflectionMethod|null
     */
    protected $reflection;

    public function __construct(ITypeSystem $typeSystem, $type, \ReflectionMethod $reflection = null)
    {
        parent::__construct($typeSystem, $type, $type);
        $this->reflection = $reflection;
    }

    public function hasMethod()
    {
        return $this->reflection !== null;
    }

    public function getReflection()
    {
        return $this->reflection;
    }
}
