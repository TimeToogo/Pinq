<?php

namespace Pinq\Queries\Functions;

/**
 * Structure of a function that receives an value and key parameter
 * and mutates the value parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ElementMutator extends MutatorBase
{
    protected function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\ValueKey($parameterExpressions);
    }

    protected function getValueParameter()
    {
        return $this->getParameters()->getValue();
    }

    /**
     * @return Parameters\ValueKey
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}
