<?php

namespace Pinq;

/**
 * The API defining all the aggregate results,
 * mainly exists for organizational purposes
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IAggregatable extends \Countable
{
    /**
     * Returns the amount of the values.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Returns whether the traversable contains no elements.
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Aggregates the values with the supplied function.
     *
     * @param callable $function The aggregate function, parameters: ($aggregate, $step)
     *
     * @return mixed
     */
    public function aggregate(callable $function);

    /**
     * Returns the maximum value.
     *
     * @param callable $function The function which will return the values.
     *
     * @return mixed
     */
    public function maximum(callable $function = null);

    /**
     * Returns the maximum value.
     *
     * @param callable $function The function which will return the values.
     *
     * @return mixed
     */
    public function minimum(callable $function = null);

    /**
     * Returns the sum of the values.
     *
     * @param callable $function The function which will return the values.
     *
     * @return int|double|null
     */
    public function sum(callable $function = null);

    /**
     * Returns the average of the values.
     *
     * @param callable $function The function which will return the values.
     *
     * @return int|double|null
     */
    public function average(callable $function = null);

    /**
     * Returns a boolean of if all the values evaluate to true.
     *
     * @param callable $function The function which will return the values.
     *
     * @return bool
     */
    public function all(callable $function = null);

    /**
     * Returns a boolean of if any of the values evaluate to true.
     *
     * @param callable $function The function which will return the values.
     *
     * @return bool
     */
    public function any(callable $function = null);

    /**
     * Returns a string of all the values concatenated by the delimiter.
     *
     * @param string   $delimiter The string to delimit the values by.
     * @param callable $function  The function which will return the values.
     *
     * @return string
     */
    public function implode($delimiter, callable $function = null);
}
