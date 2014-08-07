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

    public function __construct(Functions\ElementMutator $mutatorFunction)
    {
        $this->mutatorFunction = $mutatorFunction;
    }

    /**
     * @return Functions\ElementMutator
     */
    public function getMutatorFunction()
    {
        return $this->mutatorFunction;
    }
}
