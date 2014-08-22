<?php

namespace Pinq\Queries\Functions;

/**
 * Structure of a function that receives a outer and inner value and key
 * and returns a value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ConnectorProjection extends ProjectionBase
{
    protected function getParameterStructure(array $parameterExpressions)
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
