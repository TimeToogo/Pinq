<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Implementation of the type analysis class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TypeAnalysis implements ITypeAnalysis
{
    /**
     * @var ITypeSystem
     */
    private $typeSystem;

    /**
     * @var O\Expression
     */
    private $expression;

    /**
     * @var \SplObjectStorage
     */
    private $analysis;

    /**
     * @var \SplObjectStorage
     */
    private $metadata;

    public function __construct(
            ITypeSystem $typeSystem,
            O\Expression $expression,
            \SplObjectStorage $analysis,
            \SplObjectStorage $metadata
    ) {

        $this->typeSystem = $typeSystem;
        $this->expression = $expression;
        $this->analysis   = $analysis;
        $this->metadata   = $metadata;
    }

    public function getTypeSystem()
    {
        return $this->typeSystem;
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function getReturnedType()
    {
        return $this->getReturnTypeOf($this->expression);
    }

    public function getReturnTypeOf(O\Expression $expression)
    {
        if (!isset($this->analysis[$expression])) {
            throw new TypeException(
                    'Cannot get return type for expression of type \'%s\': the expression has no associated return type',
                    $expression->getType());
        }

        return $this->analysis[$expression];
    }

    protected function getMetadata(O\Expression $expression)
    {
        if (!isset($this->metadata[$expression])) {
            throw new TypeException(
                    'Cannot get metadata for expression of type \'%s\': the expression has no associated metadata',
                    $expression->getType());
        }

        return $this->metadata[$expression];
    }

    public function getFunction(O\FunctionCallExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getStaticMethod(O\StaticMethodCallExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getStaticField(O\StaticFieldExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getMethod(O\MethodCallExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getField(O\FieldExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getIndex(O\IndexExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getInvocation(O\InvocationExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getUnaryOperation(O\UnaryOperationExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getCast(O\CastExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getConstructor(O\NewExpression $expression)
    {
        return $this->getMetadata($expression);
    }

    public function getBinaryOperation(O\BinaryOperationExpression $expression)
    {
        return $this->getMetadata($expression);
    }
}
