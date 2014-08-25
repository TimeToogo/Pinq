<?php

namespace Pinq\Queries\Common\Source;

use Pinq\Queries\Common\ISource;
use Pinq\Queries\Segments;
use Pinq\Queries\Segments\ISegmentVisitor;

/**
 * Base class for a value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class SourceBase implements ISource
{
    public function visit(ISegmentVisitor $visitor)
    {

    }
}
