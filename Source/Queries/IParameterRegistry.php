<?php

namespace Pinq\Queries;

/**
 * Interface for request and operation query template parameters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IParameterRegistry extends \Countable
{
    /**
     * Gets the array of parameter identifiers.
     *
     * @return string[]
     */
    public function getParameters();

    /**
     * Returns the resolved parameters with the values from the supplied
     * resolved query parameters.
     *
     * @param IResolvedQuery $resolvedQuery
     *
     * @return IResolvedParameterRegistry
     * @throws \Pinq\PinqException        If there is a parameter mismatch.
     */
    public function resolve(IResolvedQuery $resolvedQuery);
}
