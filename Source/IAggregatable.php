<?php

namespace Pinq;

/**
 * Aggregating functionality interface
 */
interface IAggregatable extends \Countable {
    
    /**
     * Returns the first value
     * 
     * @return mixed The first value 
     */
    public function First();
    
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
     * Returns whether the supplied value is contained within the aggregate
     * 
     * @param mixed $Value The value to check for
     * @return boolean
     */
    public function Contains($Value);
    
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
     * @return int|null
     */
    public function Maximum(callable $Function = null);
    
    
    /**
     * Returns the maximum value.
     * 
     * @param callable $Function The function which will return the values
     * @return int|null
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
     * @return boolean|null
     */
    public function All(callable $Function = null);
    
    /**
     * Returns a boolean of if any the values evaluate to true
     * 
     * @param callable $Function The function which will return the values
     * @return boolean|null
     */
    public function Any(callable $Function = null);
    
    /**
     * Returns a string of all the values concatented with the separator
     * 
     * @param string $Delimiter The string to delimit the values by
     * @param callable $Function The function which will return the values
     * @return string
     */
    public function Implode($Delimiter, callable $Function = null);
}

