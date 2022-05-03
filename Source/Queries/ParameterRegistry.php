<?php

namespace Pinq\Queries;

use Pinq\PinqException;

/**
 * Implementation for request and operation query template parameters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterRegistry implements IParameterRegistry
{
    /**
     * The unique identifiers of the parameters as array indexes.
     *
     * @var string[]
     */
    protected $parameters;

    public function __construct(array $parameters)
    {
        if (array_unique($parameters, SORT_STRING) !== $parameters) {
            throw new PinqException(
                    'Cannot construct %s: duplicate parameter identifiers found',
                    __CLASS__);
        }

        foreach ($parameters as $parameter) {
            if (!is_string($parameter) || $parameter === '') {
                throw new PinqException(
                        'Cannot construct %s: invalid parameter name, \'%s\'',
                        __CLASS__,
                        $parameter);
            }
        }

        $this->parameters = array_values($parameters);
        sort($this->parameters);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function count(): int
    {
        return count($this->parameters);
    }

    public function resolve(IResolvedQuery $resolvedQuery)
    {
        $resolvedParameters = $resolvedQuery->getResolvedParameters();
        $resolvedParameterNames = array_keys($resolvedParameters);

        sort($resolvedParameterNames);
        if ($resolvedParameterNames !== $this->parameters) {
            throw new PinqException(
                    'Cannot resolve query parameters: resolved parameter mismatch, [%s] != [%s]',
                    implode(', ', $this->parameters),
                    implode(', ', $resolvedParameterNames));
        }

        return new ResolvedParameterRegistry($resolvedParameters);
    }
}
