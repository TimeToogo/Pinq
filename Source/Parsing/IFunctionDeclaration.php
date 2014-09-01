<?php

namespace Pinq\Parsing;

/**
 * Interface containing the declaration location of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionDeclaration
{
    /**
     * Whether the function was declared within a namespace.
     *
     * @return boolean
     */
    public function isWithinNamespace();

    /**
     * Gets the namespace which the function was declared within.
     * Null if in the global namespace.
     *
     * @return string|null
     */
    public function getNamespace();

    /**
     * Whether the function was declared within a class.
     *
     * @return boolean
     */
    public function isWithinClass();

    /**
     * Gets the class which the function was declared within.
     * Null if not defined within a class.
     *
     * @return string|null
     */
    public function getClass();

    /**
     * Whether the function was declared within a trait.
     *
     * @return boolean
     */
    public function isWithinTrait();

    /**
     * Gets the trait which the function was declared within.
     * Null if not defined within a trait.
     *
     * @return string|null
     */
    public function getTrait();

    /**
     * Whether the function was declared within a function.
     *
     * @return boolean
     */
    public function isWithinFunction();

    /**
     * Gets the function which the function was declared within.
     * Null if not defined within a function.
     *
     * @return string|null
     */
    public function getFunction();

    /**
     * Gets the amount of closures function which the function was declared within.
     *
     * @return int
     */
    public function getClosureNestingLevel();
}
