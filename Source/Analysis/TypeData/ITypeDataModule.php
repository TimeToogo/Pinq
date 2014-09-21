<?php
namespace Pinq\Analysis\TypeData;

/**
 * Interface of a type data module.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ITypeDataModule
{
    /**
     * Gets a structured array of type data.
     *
     * @see Pinq\Analysis\TypeData\DateTime::types
     *
     * @return array
     */
    public function types();

    /**
     * Gets an array of function names as keys with their
     * returning type as values.
     *
     * @see Pinq\Analysis\TypeData\InternalFunctions::dataTypes
     *
     * @return array
     */
    public function functions();
}
