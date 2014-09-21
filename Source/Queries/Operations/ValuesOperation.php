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

    final public function __construct(Common\ISource $source)
    {
        $this->source = $source;
    }

    public function getParameters()
    {
        return $this->source->getParameters();
    }

    /**
     * @return Common\ISource
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param Common\ISource $source
     *
     * @return static
     */
    public function update(Common\ISource $source)
    {
        if ($this->source === $source) {
            return $this;
        }

        return new static($source);
    }
}
