<?php
namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;


/**
 * Base interface of a query compiler.

 * Queries are compiled in to stages for flexibility.
 * Firstly, a query template is created, this can contain data about the query
 * that is parameter agnostic. That is, if there are basic or no parameters, the
 * template may contain the entire compiled query.
 *
 * An example of parameters affecting compilation output:
 * <code>
 * ->where(function ($i) use ($getter) { return $i->$getter(); })
 * </code>
 *
 * A different $getter variable could produce an entirely different compiled query,
 * hence the latter stage of compiled queries which only are agnostic of parameters
 * that can actually be parametrized.
 *
*@author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryCompiler
{

}