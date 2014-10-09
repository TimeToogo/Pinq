<?php

namespace Pinq\Analysis\Types;

use Pinq\Analysis\IType;
use Pinq\Analysis\ITypeOperation;
use Pinq\Expressions as O;

/**
 * Base class of the type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Type implements IType
{
    /**
     * @var IType
     */
    protected $parentType;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var ITypeOperation|null
     */
    protected $indexer;

    /**
     * @var ITypeOperation[]
     */
    protected $unaryOperations;

    /**
     * @var ITypeOperation[]
     */
    protected $castOperations;

    /**
     * @param string           $identifier
     * @param IType            $parentType
     * @param ITypeOperation   $indexer
     * @param ITypeOperation[] $castOperations
     * @param ITypeOperation[] $unaryOperations
     */
    public function __construct(
            $identifier,
            IType $parentType = null,
            ITypeOperation $indexer = null,
            array $castOperations = [],
            array $unaryOperations = []
    ) {
        $this->identifier      = $identifier;
        $this->parentType      = $parentType;
        $this->indexer         = $indexer;
        $this->castOperations  = $castOperations;
        $this->unaryOperations = $unaryOperations;
    }

    public function hasParentType()
    {
        return $this->parentType !== null;
    }

    public function getParentType()
    {
        return $this->parentType;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function isEqualTo(IType $type)
    {
        return $this->identifier === $type->getIdentifier();
    }

    public function getConstructor(O\NewExpression $expression)
    {
        return $this->parentType->getConstructor($expression);
    }

    public function getMethod(O\MethodCallExpression $expression)
    {
        return $this->parentType->getMethod($expression);
    }

    public function getStaticMethod(O\StaticMethodCallExpression $expression)
    {
        return $this->parentType->getStaticMethod($expression);
    }

    public function getField(O\FieldExpression $expression)
    {
        return $this->parentType->getField($expression);
    }

    public function getStaticField(O\StaticFieldExpression $expression)
    {
        return $this->parentType->getStaticField($expression);
    }

    public function getInvocation(O\InvocationExpression $expression)
    {
        return $this->parentType->getInvocation($expression);
    }

    public function getIndex(O\IndexExpression $expression)
    {
        if ($this->indexer !== null) {
            return $this->indexer;
        }

        return $this->parentType->getIndex($expression);
    }

    public function getCast(O\CastExpression $expression)
    {
        if (isset($this->castOperations[$expression->getCastType()])) {
            return $this->castOperations[$expression->getCastType()];
        }

        return $this->parentType->getCast($expression);
    }

    public function getUnaryOperation(O\UnaryOperationExpression $expression)
    {
        if (isset($this->unaryOperations[$expression->getOperator()])) {
            return $this->unaryOperations[$expression->getOperator()];
        }

        return $this->parentType->getUnaryOperation($expression);
    }
}
