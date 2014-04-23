<?php

namespace Pinq\Queries;

/**
 * The interface for a operation query, one of the const types, they
 * are all implemented as their own class in Operations namespace.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IOperation
{
    const APPLY = 0;
    const REMOVE_VALUES = 1;
    const ADD_VALUES = 2;
    const CLEAR = 3;
    const REMOVE_WHERE = 4;
    const SET_INDEX = 7;
    const UNSET_INDEX = 8;

    /**
     * @return int
     */
    public function getType();

    /**
     * @return void
     */
    public function traverse(Operations\OperationVisitor $visitor);
}
