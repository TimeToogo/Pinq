<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Common\Join;
use Pinq\Queries\Functions;

class JoinOptionsResolver extends BaseResolver implements IJoinOptionsResolver
{
    public function interpretCustomJoinFilter(IFunction $predicate)
    {
        $this->appendToHash('custom-filter');
        $this->resolveFunction($predicate);
    }

    public function interpretEqualityJoinFilter(IFunction $outerProjection, IFunction $innerProjection)
    {
        $this->appendToHash('equality-filter');
        $this->resolveFunction($outerProjection);
        $this->resolveFunction($innerProjection);
    }

    public function interpretJoinOptions(
            $isGroupJoin,
            ISourceInterpretation $sourceInterpretation,
            $defaultKeyId,
            $defaultValueId,
            array $defaultValueKeyPair = null
    ) {
        /** @var $sourceInterpretation ISourceResolver */
        $this->resolveParametersFrom($sourceInterpretation);

        if ($defaultValueKeyPair !== null) {
            $hasDefault = true;
            $this->resolveParameter($defaultValueId, $defaultValueKeyPair[0]);
            $this->resolveParameter($defaultKeyId, $defaultValueKeyPair[1]);
        } else {
            $hasDefault = false;
        }

        $this->appendToHash("join-$isGroupJoin-$hasDefault");
    }
}
