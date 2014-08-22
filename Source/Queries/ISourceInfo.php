<?php
namespace Pinq\Queries;

/**
 * Interface for a query source data container.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISourceInfo
{
    /**
     * Gets a unique string representing the source of the query.
     *
     * @return string
     */
    public function getHash();

    /**
     * Whether the source is the equal to supplied source.
     *
     * @param ISourceInfo $sourceInfo
     *
     * @return boolean
     */
    public function equals(ISourceInfo $sourceInfo);
}
