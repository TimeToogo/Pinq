<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\IScopeInterpretation;

/**
 * Interface for scope interpreters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeInterpreter
{
    /**
     * @param string $segmentId
     *
     * @return ISourceInterpreter
     */
    public function buildSourceInterpreter($segmentId);

    /**
     * @param string $segmentId
     *
     * @return IJoinOptionsInterpreter
     */
    public function buildJoinOptionsInterpreter($segmentId);

    /**
     * @return IScopeInterpretation
     */
    public function getInterpretation();

    /**
     * Interprets the supplied scope expression.
     *
     * @param O\Expression $expression
     *
     * @return void
     */
    public function interpretScope(O\Expression $expression);
}
