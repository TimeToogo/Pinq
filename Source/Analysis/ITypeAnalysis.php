<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Interface of a type analysis.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ITypeAnalysis extends ITyped
{
    /**
     * Gets the analysed expression tree.
     *
     * @return O\Expression
     */
    public function getExpression();

    /**
     * Gets the returned type analysed expression.
     *
     * @return IType
     */
    public function getReturnedType();

    /**
     * Gets the returned type of the supplied expression.
     *
     * @param O\Expression $expression
     *
     * @return IType
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getReturnTypeOf(O\Expression $expression);

    /**
     * Gets the type data for the supplied function expression.
     *
     * @param O\FunctionCallExpression $expression
     *
     * @return IFunction
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getFunction(O\FunctionCallExpression $expression);

    /**
     * Gets the type data for the supplied static method expression.
     *
     * @param O\StaticMethodCallExpression $expression
     *
     * @return IMethod
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getStaticMethod(O\StaticMethodCallExpression $expression);

    /**
     * Gets the type data for the supplied static field expression.
     *
     * @param O\StaticFieldExpression $expression
     *
     * @return IField
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getStaticField(O\StaticFieldExpression $expression);

    /**
     * Gets the type data for the supplied method expression.
     *
     * @param O\MethodCallExpression $expression
     *
     * @return IMethod
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getMethod(O\MethodCallExpression $expression);

    /**
     * Gets the type data for the supplied field expression.
     *
     * @param O\FieldExpression $expression
     *
     * @return IField
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getField(O\FieldExpression $expression);

    /**
     * Gets the type data for the supplied index expression.
     *
     * @param O\IndexExpression $expression
     *
     * @return ITypeOperation
     * @throws TypeException  if the supplied expression was not in the analysed expression tree.
     */
    public function getIndex(O\IndexExpression $expression);

    /**
     * Gets the type data for the supplied invocation expression.
     *
     * @param O\InvocationExpression $expression
     *
     * @return ITypeOperation
     * @throws TypeException  if the supplied expression was not in the analysed expression tree.
     */
    public function getInvocation(O\InvocationExpression $expression);

    /**
     * Gets the type data for the supplied unary operation operation.
     *
     * @param O\UnaryOperationExpression $expression
     *
     * @return ITypeOperation
     * @throws TypeException  if the supplied expression was not in the analysed expression tree.
     */
    public function getUnaryOperation(O\UnaryOperationExpression $expression);

    /**
     * Gets the type data for the supplied unary cast operation.
     *
     * @param O\CastExpression $expression
     *
     * @return ITypeOperation
     * @throws TypeException  if the supplied expression was not in the analysed expression tree.
     */
    public function getCast(O\CastExpression $expression);

    /**
     * Gets the type data for the supplied new operation.
     *
     * @param O\NewExpression $expression
     *
     * @return IConstructor
     * @throws TypeException if the supplied expression was not in the analysed expression tree.
     */
    public function getConstructor(O\NewExpression $expression);

    /**
     * Gets the type data for the supplied binary operation.
     *
     * @param O\BinaryOperationExpression $expression
     *
     * @return IBinaryOperation
     * @throws TypeException    if the supplied expression was not in the analysed expression tree.
     */
    public function getBinaryOperation(O\BinaryOperationExpression $expression);
}
