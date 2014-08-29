<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the operation template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationTemplate extends QueryTemplate implements IOperationTemplate
{
    public function __construct(
            Queries\IOperationQuery $query,
            Parameters\ParameterRegistry $structuralParameters
    ) {
        parent::__construct($query, $query->getParameters(), $structuralParameters);
    }
}
