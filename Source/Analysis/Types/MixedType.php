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

    protected function unsupported(O\Expression $expression, $message)
    {
        return new TypeException(
                'Type does not support expression \'%s\': %s',
                $expression->compileDebug(),
                $message);
    }

    public function getConstructor(O\NewExpression $expression)
    {
        throw $this->unsupported($expression, 'constructor is not supported');
    }

    public function getMethod(O\MethodCallExpression $expression)
    {
        throw $this->unsupported($expression, 'method is not supported');
    }

    public function getStaticMethod(O\StaticMethodCallExpression $expression)
    {
        throw $this->unsupported($expression, 'static method is not supported');
    }

    public function getField(O\FieldExpression $expression)
    {
        throw $this->unsupported($expression, 'field is not supported');
    }

    public function getStaticField(O\StaticFieldExpression $expression)
    {
        throw $this->unsupported($expression, 'static field is not supported');
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
        throw $this->unsupported($expression, 'cast is not supported');
    }

    public function getUnaryOperation(O\UnaryOperationExpression $expression)
    {
        throw $this->unsupported($expression, 'unary operator is not supported');
    }
}