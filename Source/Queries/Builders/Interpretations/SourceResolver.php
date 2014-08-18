<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Expressions as O;
use Pinq\Queries\Common;
use Pinq\Queries\Functions;
use Pinq\Queries\Segments;
use Pinq\Queries;

/**
 * Implementation of the source resolver.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SourceResolver extends BaseResolver implements ISourceResolver
{
    public function interpretArrayOrIterator($sourceId, $arrayOrIterator)
    {
        $this->resolveParameter($sourceId, $arrayOrIterator);
    }

    public function interpretSingleValue($sourceId, $value)
    {
        $this->resolveParameter($sourceId, $value);
    }

    public function interpretQueryScope($sourceId, IScopeInterpretation $scopeInterpretation)
    {
        //Scope will already be resolved from the scope interpretation
        $this->appendToHash($sourceId);
    }
}