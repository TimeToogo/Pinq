<?php

namespace Pinq\Analysis;

/**
 * Interface of an object type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IObjectType extends IType
{
    /**
     * Gets the qualified class name.
     *
     * @return string
     */
    public function getClassType();

    /**
     * Gets the reflection of the class type.
     *
     * @return \ReflectionClass
     */
    public function getReflection();
}
