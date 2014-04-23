<?php

namespace Pinq\Queries;

/**
 * The interface for a request query, one of the const types, they
 * are all implemented as their own class in Requests namespace.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRequest
{
    const VALUES = 0;
    const COUNT = 1;
    const EXISTS = 2;
    const FIRST = 3;
    const LAST = 4;
    const CONTAINS = 5;
    const AGGREGATE = 6;
    const MAXIMUM = 7;
    const MINIMUM = 8;
    const SUM = 9;
    const AVERAGE = 10;
    const ALL = 11;
    const ANY = 12;
    const IMPLODE = 13;
    const GET_INDEX = 14;
    const ISSET_INDEX = 15;

    /**
     * @return int
     */
    public function getType();

    /**
     * @return mixed
     */
    public function traverse(Requests\RequestVisitor $visitor);
}
