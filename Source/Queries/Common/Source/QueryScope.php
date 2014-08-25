<?php

namespace Pinq\Queries\Common\Source;

use Pinq\Queries\Segments\ISegmentVisitor;
use Pinq\Queries\Segments;
use Pinq\Queries;

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

    public function getParameters()
    {
        return $this->scope->getParameters();
    }

    public function visit(ISegmentVisitor $visitor)
    {
        $this->scope->visit($visitor);
    }

    /**
     * @param Queries\IScope $scope
     *
     * @return QueryScope
     */
    public function update(Queries\IScope $scope)
    {
        if ($this->scope === $scope) {
            return $this;
        }

        return new self($scope);
    }
}
