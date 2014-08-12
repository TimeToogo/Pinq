<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Base class of a mutator function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class MutatorBase extends FunctionBase
{
    /**
     * @var boolean
     */
    protected $valueParameterIsReference;

    protected function initialize()
    {
        $valueParameter = $this->getValueParameter();

        $this->valueParameterIsReference = $valueParameter !== null && $valueParameter->isPassedByReference();
    }

    /**
     * @return O\ParameterExpression|null
     */
    abstract protected function getValueParameter();

    final public function valueParameterIsReference()
    {
        return $this->valueParameterIsReference;
    }

    protected function dataToSerialize()
    {
        return $this->valueParameterIsReference;
    }

    protected function unserializeData($data)
    {
        $this->valueParameterIsReference = $data;
    }
}
