<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Interface of a binary operation between two types.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IBinaryOperation extends ITyped
{
    /**
     * Gets the type of the left operand.
     *
     * @return IType
     */
    public function getLeftOperandType();

    /**
     * Gets the operator of the binary operation.
     *
     * @return int The binary operator from the Expressions\Operators\Binary::* constants
     */
    public function getOperator();

    /**
     * Gets the type of the right operand.
     *
     * @return IType
     */
    public function getRightOperandType();

    /**
     * Gets the returned type of the binary operation.
     *
     * @return IType
     */
    public function getReturnType();
}