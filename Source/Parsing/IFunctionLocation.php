<?php

namespace Pinq\Parsing;

/**
 * Interface containing the reflection data of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionLocation
{
    /**
     * Gets the file path where the function was declared.
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Whether the function is defined in a namespace.
     *
     * @return boolean
     */
    public function inNamespace();

    /**
     * Gets the namespace which the function was defined in.
     * Null if in the global namespace.
     *
     * @return string|null
     */
    public function getNamespace();

    /**
     * Gets the start line of the function.
     *
     * @return int
     */
    public function getStartLine();

    /**
     * Gets the end line of the function.
     *
     * @return int
     */
    public function getEndLine();

    /**
     * Gets a hash of the function location.
     *
     * @return string
     */
    public function getHash();
}
