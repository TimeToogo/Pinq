<?php

namespace Pinq\Analysis\Functions;

use Pinq\Analysis\IFunction;
use Pinq\Analysis\ITypeSystem;
use Pinq\Analysis\Typed;

/**
 * Implementation of the function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Func extends Typed implements IFunction
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \ReflectionFunction
     */
    protected $reflection;

    /**
     * @var string
     */
    protected $returnType;

    public function __construct(ITypeSystem $typeSystem, $name, $returnType)
    {
        parent::__construct($typeSystem);
        $this->name       = $name;
        $this->reflection = new \ReflectionFunction($name);
        $this->returnType = $returnType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getReturnType()
    {
        return $this->typeSystem->getType($this->returnType);
    }

    public function getReturnTypeWithArguments(array $staticArguments)
    {
        return $this->typeSystem->getType($this->returnType);
    }
}
