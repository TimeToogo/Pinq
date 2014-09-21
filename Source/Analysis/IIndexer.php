<?php

namespace Pinq\Analysis;

/**
 * Interface of a indexer of a type.
 * <code>
 * $val['index'];
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IIndexer extends ITypeOperation
{
    /**
     * Gets the return type of the indexer with the supplied index value.
     *
     * @param mixed $index
     *
     * @return IType
     */
    public function getReturnTypeOfIndex($index);
}
