<?php

namespace Pinq\Queries\Functions;

/**
 * Structure of function that takes a value and key parameter
 * and returns a value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ElementProjection extends ProjectionBase
{
    protected function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\ValueKey($parameterExpressions);
    }

    /**
     * {@inheritDoc}
     *
     * @return Parameters\ValueKey
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}
