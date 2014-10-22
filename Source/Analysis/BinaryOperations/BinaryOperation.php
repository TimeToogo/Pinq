<?php

namespace Pinq\Analysis\BinaryOperations;

use Pinq\Analysis\IBinaryOperation;
use Pinq\Analysis\ITypeSystem;
use Pinq\Analysis\Typed;

/**
 * Implementation of the binary operation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BinaryOperation extends Typed implements IBinaryOperation
{
    /**
     * @var string
     */
    protected $leftOperandType;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var string
     */
    protected $rightOperandType;

    /**
     * @var string
     */
    protected $returnType;

    public function __construct(ITypeSystem $typeSystem, $leftOperandType, $operator, $rightOperandType, $returnType)
    {
        parent::__construct($typeSystem);
        $this->leftOperandType  = $leftOperandType;
        $this->operator         = $operator;
        $this->rightOperandType = $rightOperandType;
        $this->returnType       = $returnType;
    }

    public function getLeftOperandType()
    {
        return $this->typeSystem->getType($this->leftOperandType);
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getRightOperandType()
    {
        return $this->typeSystem->getType($this->rightOperandType);
    }

    public function getReturnType()
    {
        return $this->typeSystem->getType($this->returnType);
    }
}
