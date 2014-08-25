<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\Functions;

/**
 * Base class for an operation query containing a mutator function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class MutatorOperation extends Operation
{
    /**
     * @var Functions\ElementMutator
     */
    private $mutatorFunction;

    final public function __construct(Functions\ElementMutator $mutatorFunction)
    {
        $this->mutatorFunction = $mutatorFunction;
    }

    public function getParameters()
    {
        return $this->mutatorFunction->getParameterIds();
    }

    /**
     * @return Functions\ElementMutator
     */
    public function getMutatorFunction()
    {
        return $this->mutatorFunction;
    }

    /**
     * @param Functions\ElementMutator $mutatorFunction
     *
     * @return static
     */
    public function update(Functions\ElementMutator $mutatorFunction)
    {
        if ($this->mutatorFunction === $mutatorFunction) {
            return $this;
        }

        return new static($mutatorFunction);
    }
}
