<?php

namespace Pinq\Queries\Common\Source;

use Pinq\Queries;
use Pinq\Queries\Segments;
use Pinq\Queries\Segments\ISegmentVisitor;

/**
 * Query scope value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class QueryScope extends SourceBase
{
    /**
     * @var Queries\IScope
     */
    private $scope;

    public function __construct(Queries\IScope $scope)
    {
        $this->scope = $scope;
    }

    public function getType()
    {
        return self::QUERY_SCOPE;
    }

    /**
     * @return Queries\IScope
     */
    public function getScope()
    {
        return $this->scope;
    }

    public function visit(ISegmentVisitor $visitor)
    {
        $visitor->visit($this->scope);
    }
}
