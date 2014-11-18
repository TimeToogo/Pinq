<?php

namespace Pinq\Analysis\Types;

use Pinq\Analysis\INativeType;
use Pinq\Analysis\IType;
use Pinq\Analysis\TypeException;
use Pinq\Expressions as O;

/**
 * Implementation of the mixed type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MixedType extends NativeType
{
    public function __construct($identifier)
    {
        parent::__construct($identifier, null, INativeType::TYPE_MIXED);
    }

    public function isParentTypeOf(IType $type)
    {
        return true;
    }

    protected function unsupported(O\Expression $expression, $message, array $formatValues = [])
    {
        return new TypeException(
                'Type %s does not support expression \'%s\': %s',
                $this->identifier,
                $expression->compileDebug(),
                vsprintf($message, $formatValues));
    }

    public function getConstructor(O\NewExpression $expression)
    {
        throw $this->unsupported($expression, 'constructor is not supported');
    }

    public function getMethod(O\MethodCallExpression $expression)
    {
        throw $this->unsupported($expression, 'method %s is not supported', [$expression->getName()->compileDebug()]);
    }

    public function getStaticMethod(O\StaticMethodCallExpression $expression)
    {
        throw $this->unsupported($expression, 'static method %s is not supported', [$expression->getName()->compileDebug()]);
    }

    public function getField(O\FieldExpression $expression)
    {
        throw $this->unsupported($expression, 'field %s is not supported', [$expression->getName()->compileDebug()]);
    }

    public function getStaticField(O\StaticFieldExpression $expression)
    {
        throw $this->unsupported($expression, 'static field %s is not supported', [$expression->getName()->compileDebug()]);
    }

    public function getInvocation(O\InvocationExpression $expression)
    {
        throw $this->unsupported($expression, 'invocation is not supported');
    }

    public function getIndex(O\IndexExpression $expression)
    {
        throw $this->unsupported($expression, 'indexer is not supported');
    }

    public function getCast(O\CastExpression $expression)
    {
        throw $this->unsupported($expression, 'cast \'%s\' is not supported', [$expression->getCastType()]);
    }

    public function getUnaryOperation(O\UnaryOperationExpression $expression)
    {
        throw $this->unsupported($expression, 'unary operator \'%s\' is not supported', [$expression->getOperator()]);
    }
}
