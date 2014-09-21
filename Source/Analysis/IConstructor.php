<?php

namespace Pinq\Analysis;

/**
 * Interface of the constructor of a type.
 * <code>
 * new stdClass();
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IConstructor extends ITypeOperation
{
    /**
     * Whether the type has a __construct method.
     *
     * @return boolean
     */
    public function hasMethod();

    /**
     * Gets the reflection of the constructor.
     * Null if there is no __construct method.
     *
     * @return \ReflectionMethod|null
     */
    public function getReflection();
}
