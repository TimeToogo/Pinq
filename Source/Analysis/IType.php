<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Interface of a type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IType
{
    /**
     * Whether the type has a parent type.
     *
     * @return boolean
     */
    public function hasParentType();

    /**
     * Gets the parent type or null if their is no parent.
     *
     * @return IType|null
     */
    public function getParentType();

    /**
     * Whether the supplied type is equivalent to the current type.
     *
     * @param IType $type
     *
     * @return boolean
     */
    public function isEqualTo(IType $type);

    /**
     * Whether the supplied type is a subtype of or equal to the current type.
     *
     * @param IType $type
     *
     * @return boolean
     */
    public function isParentTypeOf(IType $type);

    /**
     * Gets a unique string representation of the type.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Gets the supplied expression matches the type's constructor.
     *
     * @param O\NewExpression $expression
     *
     * @return IConstructor
     * @throws TypeException if the constructor is not supported.
     */
    public function getConstructor(O\NewExpression $expression);

    /**
     * Gets the matched method of the supplied expression.
     *
     * @param O\MethodCallExpression $expression
     *
     * @return IMethod
     * @throws TypeException if the method is not supported.
     */
    public function getMethod(O\MethodCallExpression $expression);

    /**
     * Gets the matched method of the supplied expression.
     *
     * @param O\StaticMethodCallExpression $expression
     *
     * @return IMethod
     * @throws TypeException if the static method is not supported.
     */
    public function getStaticMethod(O\StaticMethodCallExpression $expression);

    /**
     * Gets the matched field of the supplied expression.
     *
     * @param O\FieldExpression $expression
     *
     * @return IField
     * @throws TypeException if the field is not supported.
     */
    public function getField(O\FieldExpression $expression);

    /**
     * Gets the matched field of the supplied expression.
     *
     * @param O\StaticFieldExpression $expression
     *
     * @return IField
     * @throws TypeException if the static field is not supported.
     */
    public function getStaticField(O\StaticFieldExpression $expression);

    /**
     * Get the supplied index expression matches the type.
     *
     * @param O\IndexExpression $expression
     *
     * @return ITypeOperation
     * @throws TypeException  if the indexer is not supported.
     */
    public function getIndex(O\IndexExpression $expression);

    /**
     * Gets the invocation expression matches the type.
     *
     * @param O\InvocationExpression $expression
     *
     * @return ITypeOperation|IMethod
     * @throws TypeException          if the invocation is not supported.
     */
    public function getInvocation(O\InvocationExpression $expression);

    /**
     * Gets the matched unary operator from the supplied expression.
     *
     * @param O\CastExpression $expression
     *
     * @return ITypeOperation|IMethod
     * @throws TypeException          if the cast is not supported.
     */
    public function getCast(O\CastExpression $expression);

    /**
     * Gets the matched unary operator from the supplied expression.
     *
     * @param O\UnaryOperationExpression $expression
     *
     * @return ITypeOperation|IMethod
     * @throws TypeException          if the unary operation is not supported.
     */
    public function getUnaryOperation(O\UnaryOperationExpression $expression);
}
