<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the request template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestTemplate extends QueryTemplate implements IRequestTemplate
{
    public function __construct(
            Queries\IRequestQuery $query,
            Parameters\ParameterRegistry $structuralParameters
    ) {
        parent::__construct($query, $query->getParameters(), $structuralParameters);
    }
}
