<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries;

/**
 * Interface for scope interpreters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISourceInterpretation
{
    public function interpretArrayOrIterator($sourceId, $arrayOrIterator);

    public function interpretSingleValue($sourceId, $value);

    public function interpretQueryScope($sourceId, IScopeInterpretation $scopeInterpretation);
}
