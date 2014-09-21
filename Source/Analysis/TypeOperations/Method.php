<?php

namespace Pinq\Analysis\TypeOperations;

use Pinq\Analysis\IMethod;
use Pinq\Analysis\ITypeSystem;

/**
 * Implementation of the method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Method extends TypeOperation implements IMethod
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \ReflectionMethod
     */
    protected $reflection;

    public function __construct(ITypeSystem $typeSystem, $sourceType, \ReflectionMethod $reflection, $returnType)
    {
        parent::__construct($typeSystem, $sourceType, $returnType);
        $this->name = $reflection->getName();
        $this->reflection = $reflection;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getReturnTypeWithArguments(array $staticArguments)
    {
        return $this->getReturnType();
    }
}
