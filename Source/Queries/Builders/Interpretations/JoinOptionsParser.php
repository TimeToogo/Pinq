<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Common\Join;
use Pinq\Queries\Functions;

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
        $this->joinFilter = new Join\Filter\Custom($this->buildFunction(
                $predicate,
                Functions\ConnectorProjection::factory()
        ));
    }

    public function interpretEqualityJoinFilter(IFunction $outerProjection, IFunction $innerProjection)
    {
        $this->joinFilter = new Join\Filter\Equality(
                $this->buildFunction($outerProjection, Functions\ElementProjection::factory()),
                $this->buildFunction($innerProjection, Functions\ElementProjection::factory())
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
                $sourceInterpretation->getSource(),
                $isGroupJoin,
                $this->joinFilter,
                $hasDefault,
                $hasDefault ? $defaultValueId : null,
                $hasDefault ? $defaultKeyId : null
        );
    }
}
