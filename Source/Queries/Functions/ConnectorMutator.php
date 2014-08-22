<?php

namespace Pinq\Queries\Functions;

/**
 * Structure of a function that receives a outer and inner value and key
 * and mutates the outer value parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ConnectorMutator extends MutatorBase
{
    protected function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\OuterInnerValueKey($parameterExpressions);
    }

    protected function getValueParameter()
    {
        return $this->getParameters()->getOuterValue();
    }

    /**
     * @return Parameters\OuterInnerValueKey
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}
