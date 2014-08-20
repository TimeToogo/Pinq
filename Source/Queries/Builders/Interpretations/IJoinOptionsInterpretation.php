<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries;

/**
 * Interface for scope interpreters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoinOptionsInterpretation
{
    public function interpretCustomJoinFilter(IFunction $predicate);

    public function interpretEqualityJoinFilter(IFunction $outerProjection, IFunction $innerProjection);

    public function interpretJoinOptions(
            $isGroupJoin,
            ISourceInterpretation $sourceInterpretation,
            $defaultKeyId,
            $defaultValueId,
            array $defaultValueKeyPair = null
    );
}
