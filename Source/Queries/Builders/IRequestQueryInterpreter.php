<?php

namespace Pinq\Queries\Builders;

use Pinq\Queries\Builders\Interpretations\IRequestInterpretation;

/**
 * Interface for a request query expression interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestQueryInterpreter extends IQueryInterpreter
{
    /**
     * Gets the request interpretation.
     *
     * @return IRequestInterpretation
     */
    public function getInterpretation();
}
