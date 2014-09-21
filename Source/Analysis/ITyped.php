<?php
namespace Pinq\Analysis;

/**
 * Interface for classes using the type system.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ITyped
{
    /**
     * Gets the type system.
     *
     * @return ITypeSystem
     */
    public function getTypeSystem();
}
