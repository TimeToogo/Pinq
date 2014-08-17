<?php

namespace Pinq\Queries\Common;

use Pinq\Queries\Segments;
use Pinq\Queries\Segments\ISegmentVisitor;

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
     * @param ISegmentVisitor $visitor
     *
     * @return ISource
     */
    public function visit(ISegmentVisitor $visitor);
}
