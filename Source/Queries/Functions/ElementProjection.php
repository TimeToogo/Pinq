<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Structure of function that takes a value and key parameter
 * and returns a value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ElementProjection extends ProjectionBase
{
    public function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\ValueKey($parameterExpressions);
    }

    /**
     * @return Parameters\ValueKey
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}
