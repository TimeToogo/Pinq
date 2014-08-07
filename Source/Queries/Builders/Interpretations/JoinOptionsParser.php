<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Common\Join;
use Pinq\Queries\Functions;
use Pinq\Queries\Segments;

class JoinOptionsParser extends BaseParser implements IJoinOptionsParser
{
    /**
     * @var Join\IFilter
     */
    protected $joinFilter;

    /**
     * @var Join\Options
     */
    protected $joinOptions;

    public function getJoinOptions()
    {
        return $this->joinOptions;
    }

    public function interpretCustomJoinFilter(IFunction $predicate)
    {
        $this->joinFilter = new Join\Filter\Custom($this->requireFunction(
                $predicate,
                Functions\ConnectorProjection::factory()
        ));
    }

    public function interpretEqualityJoinFilter(IFunction $outerProjection, IFunction $innerProjection)
    {
        $this->joinFilter = new Join\Filter\Equality(
                $this->requireFunction($outerProjection, Functions\ElementProjection::factory()),
                $this->requireFunction($innerProjection, Functions\ElementProjection::factory())
        );
    }

    public function interpretJoinOptions(
            $isGroupJoin,
            ISourceInterpretation $sourceInterpretation,
            $defaultKeyId,
            $defaultValueId,
            array $defaultValueKeyPair = null
    ) {
        /* @var $sourceInterpretation ISourceParser */

        $hasDefault = $defaultValueKeyPair !== null;

        $this->joinOptions = new Join\Options(
                $this->requireSource($sourceInterpretation),
                $isGroupJoin,
                $this->joinFilter,
                $hasDefault,
                $hasDefault ? $this->requireParameter($defaultValueId) : null,
                $hasDefault ? $this->requireParameter($defaultKeyId) : null
        );
    }
}
