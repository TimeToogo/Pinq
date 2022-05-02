<?php

namespace Pinq\Queries;

use Pinq\PinqException;

/**
 * Implementation of the resolved parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResolvedParameterRegistry implements IResolvedParameterRegistry
{
    /**
     * The resolved values of the parameters, indexed by their
     * respective parameter identifier.
     *
     * @var mixed[]
     */
    protected $resolvedParameters;

    public function __construct(array $resolvedParameters)
    {
        $this->resolvedParameters = $resolvedParameters;
    }

    /**
     * Returns an empty resolved parameter registry.
     *
     * @return ResolvedParameterRegistry
     */
    public static function none()
    {
        return new self([]);
    }

    public function getResolvedParameters()
    {
        return $this->resolvedParameters;
    }

    public function count(): int
    {
        return count($this->resolvedParameters);
    }

    public function offsetExists($parameter): bool
    {
        return array_key_exists($parameter, $this->resolvedParameters);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($parameter)
    {
        if (!$this->offsetExists($parameter)) {
            throw new PinqException(
                    'Cannot retrieve parameter %s: parameter is not resolved',
                    $parameter);
        }

        return $this->resolvedParameters[$parameter];
    }

    public function offsetSet($offset, $value): void
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function offsetUnset($offset): void
    {
        throw PinqException::notSupported(__METHOD__);
    }
}
