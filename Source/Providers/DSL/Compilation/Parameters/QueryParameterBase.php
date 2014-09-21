<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Base class of the query parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryParameterBase implements IQueryParameter
{
    /**
     * @var IParameterHasher
     */
    protected $parameterHasher;

    /**
     * @var mixed
     */
    protected $data;

    public function __construct(IParameterHasher $parameterHasher, $data = null)
    {
        $this->parameterHasher = $parameterHasher;
        $this->data            = $data;
    }

    /**
     * @return IParameterHasher
     */
    final public function getHasher()
    {
        return $this->parameterHasher;
    }

    final public function getData()
    {
        return $this->data;
    }

    public function evaluate(IResolvedParameterRegistry $parameters, &$hash)
    {
        $value = $this->doEvaluate($parameters);
        $hash  = $this->parameterHasher->hash($value);

        return $value;
    }

    public function hash(IResolvedParameterRegistry $parameters)
    {
        return $this->parameterHasher->hash($this->doEvaluate($parameters));
    }

    abstract protected function doEvaluate(IResolvedParameterRegistry $parameterRegistry);
}
