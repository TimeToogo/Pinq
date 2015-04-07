<?php
namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Interface of the query parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryParameter
{
    /**
     * Gets any associated data of the parameter.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Gets the query parameter value with the supplied resolved parameters.
     * A unique identifier is assigned to the $hash parameter.
     *
     * @param IResolvedParameterRegistry $parameters
     * @param string                     $hash The hash will be set to this variable
     *
     * @return mixed The parameter value
     */
    public function evaluate(IResolvedParameterRegistry $parameters, &$hash);

    /**
     * Returns a unique identifier of the query parameter according the resolved values.
     *
     * @param IResolvedParameterRegistry $parameters
     *
     * @return string
     */
    public function hash(IResolvedParameterRegistry $parameters);
}
