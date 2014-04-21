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
    const Values = 0;
    const Count = 1;
    const Exists = 2;
    const First = 3;
    const Last = 4;
    const Contains = 5;
    const Aggregate = 6;
    const Maximum = 7;
    const Minimum = 8;
    const Sum = 9;
    const Average = 10;
    const All = 11;
    const Any = 12;
    const Implode = 13;
    const GetIndex = 14;
    const IssetIndex = 15;

    /**
     * @return int
     */
    public function GetType();

    /**
     * @return mixed
     */
    public function Traverse(Requests\RequestVisitor $Visitor);
}
