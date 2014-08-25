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
    const SINGLE_VALUE      = 1;
    const QUERY_SCOPE       = 2;

    /**
     * @return int
     */
    public function getType();

    /**
     * @return string[]
     */
    public function getParameters();

    /**
     * @param ISegmentVisitor $visitor
     *
     * @return void
     */
    public function visit(ISegmentVisitor $visitor);
}
