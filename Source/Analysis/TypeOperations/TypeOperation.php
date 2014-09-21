<?php

namespace Pinq\Analysis\TypeOperations;

use Pinq\Analysis\ITypeOperation;
use Pinq\Analysis\ITypeSystem;
use Pinq\Analysis\Typed;

/**
 * Implementation of the type operation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TypeOperation extends Typed implements ITypeOperation
{
    /**
     * @var string
     */
    protected $sourceType;

    /**
     * @var string
     */
    protected $returnType;

    public function __construct(ITypeSystem $typeSystem, $sourceType, $returnType)
    {
        parent::__construct($typeSystem);
        $this->sourceType = $sourceType;
        $this->returnType = $returnType;
    }

    public function getSourceType()
    {
        return $this->typeSystem->getType($this->sourceType);
    }

    public function getReturnType()
    {
        return $this->typeSystem->getType($this->returnType);
    }
}
