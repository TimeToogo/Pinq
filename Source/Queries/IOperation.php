<?php

namespace Pinq\Queries;

use Pinq\Queries\Operations\IOperationVisitor;

/**
 * The interface for a operation query, one of the const types, they
 * are all implemented as their own class in Operations namespace.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperation
{
    const APPLY         = 0;
    const JOIN_APPLY    = 1;
    const REMOVE_VALUES = 3;
    const ADD_VALUES    = 4;
    const CLEAR         = 5;
    const REMOVE_WHERE  = 6;
    const SET_INDEX     = 7;
    const UNSET_INDEX   = 8;

    /**
     * @return int
     */
    public function getType();

    /**
     * @return string[]
     */
    public function getParameters();

    /**
     * @param Operations\IOperationVisitor $visitor
     *
     * @return mixed
     */
    public function traverse(IOperationVisitor $visitor);
}
