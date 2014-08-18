<?php

namespace Pinq\Queries\Common\Source;

/**
 * Base class for parameter value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ParameterSourceBase extends SourceBase
{
    /**
     * @var string
     */
    protected $parameterId;

    public function __construct($parameterId)
    {
        $this->parameterId = $parameterId;
    }

    public function getParameters()
    {
        return [$this->parameterId];
    }

    /**
     * Gets the parameter id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->parameterId;
    }
}
