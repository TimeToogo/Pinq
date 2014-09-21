<?php

namespace Pinq\Analysis;

/**
 * Interface of a type operation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ITypeOperation extends ITyped
{
    /**
     * Gets the type being operated on.
     *
     * @return IType
     */
    public function getSourceType();

    /**
     * Gets return type of the operation.
     *
     * @return IType
     */
    public function getReturnType();
}
