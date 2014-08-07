<?php

namespace Pinq\Queries\Common\Source;

use Pinq\Queries;
use Pinq\Queries\Segments;

/**
 * Query scope value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class QueryScope extends Base
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

    public function visit(Segments\SegmentVisitor $visitor)
    {
        $visitor->visit($this->scope);
    }
}
