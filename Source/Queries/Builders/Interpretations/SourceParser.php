<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\Common\ISource;
use Pinq\Queries\Common\Source;
use Pinq\Queries;

class SourceParser extends BaseParser implements ISourceParser
{
    /**
     * @var ISource
     */
    protected $source;

    public function getSource()
    {
        return $this->source;
    }

    public function interpretArrayOrIterator($sourceId, $arrayOrIterator)
    {
        $this->source = new Source\ArrayOrIterator($sourceId);
    }

    public function interpretSingleValue($sourceId, $value)
    {
        $this->source = new Source\SingleValue($sourceId);
    }

    public function interpretQueryScope($sourceId, IScopeInterpretation $scopeInterpretation)
    {
        /* @var $scopeInterpretation IScopeParser */
        $this->source = new Source\QueryScope($scopeInterpretation->getScope());
    }
}
