<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Structure of a function that recieves a outer and inner value and key
 * and returns a value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ConnectorProjection extends ProjectionBase
{
    public function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\OuterInnerValueKey($parameterExpressions);
    }

    /**
     * @return Parameters\OuterInnerValueKey
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}
