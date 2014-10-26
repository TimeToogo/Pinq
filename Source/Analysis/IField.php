<?php

namespace Pinq\Analysis;

/**
 * Interface of a field of a type.
 * <code>
 * $val->field;
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IField extends ITypeOperation
{
    /**
     * Gets the name of the field.
     *
     * @return string
     */
    public function getName();

    /**
     * Whether the field is static.
     *
     * @return boolean
     */
    public function isStatic();
}
