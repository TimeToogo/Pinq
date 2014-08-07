<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\Common;

/**
 * Base class for an operation query for with a range of values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ValuesOperation extends Operation
{
    /**
     * @var Common\ISource
     */
    private $source;

    public function __construct(Common\ISource $source)
    {
        $this->source = $source;
    }

    /**
     * @return Common\ISource
     */
    public function getSource()
    {
        return $this->source;
    }
}
