<?php

namespace Pinq\Parsing;

/**
 * Interface of a set of resolved magic constants for a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IMagicConstants
{
    /**
     * Gets the value of the __DIR__ magic constant.
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Gets the value of the __FILE__ magic constant.
     *
     * @return string
     */
    public function getFile();

    /**
     * Gets the value of the __NAMESPACE__ magic constant.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Gets the value of the __CLASS__ magic constant.
     *
     * @return string
     */
    public function getClass();

    /**
     * Gets the value of the __TRAIT__ magic constant.
     *
     * @return string
     */
    public function getTrait();

    /**
     * Gets the value of the __FUNCTION__ magic constant.
     *
     * @param boolean $isWithinClosure If within a closure defined in the function
     *
     * @return string
     */
    public function getFunction($isWithinClosure);

    /**
     * Gets the value of the __METHOD__ magic constant.
     *
     * @param boolean $isWithinClosure If within a closure defined in the function
     *
     * @return string
     */
    public function getMethod($isWithinClosure);
}
