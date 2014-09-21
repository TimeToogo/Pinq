<?php

namespace Pinq\Analysis;

/**
 * Interface of a composite type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ICompositeType extends IType
{
    /**
     * Gets the composed types.
     *
     * @return IType[]
     */
    public function getComposedTypes();
}
