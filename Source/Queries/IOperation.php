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
    const JOIN_APPLY = 1;
    const EQUALITY_JOIN_APPLY = 2;
    const REMOVE_VALUES = 3;
    const ADD_VALUES = 4;
    const CLEAR = 5;
    const REMOVE_WHERE = 6;
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
