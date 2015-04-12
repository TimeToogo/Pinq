<?php

namespace Pinq\Analysis;

/**
 * Interface of a type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface INativeType extends IType
{
    const TYPE_MIXED    = 'native:mixed';
    const TYPE_NUMERIC  = 'native:numeric';
    const TYPE_STRING   = 'native:string';
    const TYPE_INT      = 'native:int';
    const TYPE_ARRAY    = 'native:array';
    const TYPE_DOUBLE   = 'native:double';
    const TYPE_BOOL     = 'native:boolean';
    const TYPE_NULL     = 'native:null';
    const TYPE_RESOURCE = 'native:resource';

    /**
     * Gets the type of the type represented by the TYPE_* constants.
     *
     * @return string
     */
    public function getTypeOfType();
}
