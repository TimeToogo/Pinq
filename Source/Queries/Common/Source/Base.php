<?php

namespace Pinq\Queries\Common\Source;

use Pinq\Queries\Common\ISource;
use Pinq\Queries\Segments;

/**
 * Base class for a value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Base implements ISource
{
    public function visit(Segments\SegmentVisitor $visitor)
    {
        return $this;
    }
}
