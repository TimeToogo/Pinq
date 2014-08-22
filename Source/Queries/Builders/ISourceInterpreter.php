<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\ISourceInterpretation;

/**
 * Interface for source interpreters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISourceInterpreter
{
    /**
     * Gets the source interpretation
     *
     * @return ISourceInterpretation
     */
    public function getInterpretation();

    /**
     * Interprets the supplied source expression.
     *
     * @param O\Expression $expression
     *
     * @return void
     */
    public function interpretSource(O\Expression $expression);
}
