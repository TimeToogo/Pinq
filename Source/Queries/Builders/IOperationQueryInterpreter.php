<?php

namespace Pinq\Queries\Builders;

use Pinq\Queries\Builders\Interpretations\IOperationInterpretation;

/**
 * Interface for a operation query expression interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationQueryInterpreter extends IQueryInterpreter
{
    /**
     * Gets the operation interpretation.
     *
     * @return IOperationInterpretation
     */
    public function getInterpretation();
}
