<?php

namespace Pinq;

/**
 * The API defining all the aggregate results,
 * mainly exists for organizational purposes
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IAggregatable extends \Countable 
{
    
    /**
     * Returns the amount of the values.
     * 
     * @return int
     */
    public function Count();
    
    /**
     * Returns whether any values exist.
     * 
     * @return boolean
     */
    public function Exists();
    
    /**
     * Aggregates the values with the supplied function
     * 
     * @param callable $Function The aggregate function
     * @return mixed
     */
    public function Aggregate(callable $Function);
    
    /**
     * Returns the maximum value.
     * 
     * @param callable $Function The function which will return the values
     * @return mixed
     */
    public function Maximum(callable $Function = null);
    
    
    /**
     * Returns the maximum value.
     * 
     * @param callable $Function The function which will return the values
     * @return mixed
     */
    public function Minimum(callable $Function = null);
    
    /**
     * Returns the sum of the values.
     * 
     * @param callable $Function The function which will return the values
     * @return int|null
     */
    public function Sum(callable $Function = null);
    
    
    /**
     * Returns the average of the values.
     * 
     * @param callable $Function The function which will return the values
     * @return double|null
     */
    public function Average(callable $Function = null);
    
    
    /**
     * Returns a boolean of if all the values evaluate to true
     * 
     * @param callable $Function The function which will return the values
     * @return bool
     */
    public function All(callable $Function = null);
    
    /**
     * Returns a boolean of if any of the values evaluate to true
     * 
     * @param callable $Function The function which will return the values
     * @return bool
     */
    public function Any(callable $Function = null);
    
    /**
     * Returns a string of all the values concatented by the delimiter
     * 
     * @param string $Delimiter The string to delimit the values by
     * @param callable $Function The function which will return the values
     * @return string
     */
    public function Implode($Delimiter, callable $Function = null);
}

