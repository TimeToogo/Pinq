<?php

namespace Pinq\Queries\Common;

use Pinq\Queries\Segments;

/**
 * Interface for a source of values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISource
{
    const ARRAY_OR_ITERATOR = 0;
    const QUERY_SCOPE       = 1;

    /**
     * @return int
     */
    public function getType();

    /**
     * @param Segments\SegmentVisitor $visitor
     *
     * @return ISource
     */
    public function visit(Segments\SegmentVisitor $visitor);
}
