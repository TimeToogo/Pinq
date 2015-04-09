<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the standard query parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StandardParameter extends QueryParameterBase
{
    /**
     * @var string
     */
    protected $parameterId;

    /**
     * @param string           $parameterId
     * @param IParameterHasher $hasher
     * @param null             $data
     */
    public function __construct($parameterId, IParameterHasher $hasher, $data = null)
    {
        parent::__construct($hasher, $data);
        $this->parameterId = $parameterId;
    }

    public function doEvaluate(IResolvedParameterRegistry $parameters)
    {
        return $parameters[$this->parameterId];
    }
}
