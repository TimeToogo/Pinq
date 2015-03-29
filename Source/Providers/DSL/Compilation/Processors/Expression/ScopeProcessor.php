<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Providers\DSL\Compilation\Processors\Visitors;
use Pinq\Queries\Segments;
use Pinq\Queries;

/**
 * Implementation of the scope processor to add expressions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeProcessor extends Visitors\ScopeProcessor
{
    /**
     * @var IExpressionProcessor
     */
    protected $expressionProcessor;

    public function __construct(Queries\IScope $scope, IExpressionProcessor $expressionProcessor)
    {
        parent::__construct($scope);

        $this->expressionProcessor = $expressionProcessor;
    }

    public function forSubScope(Queries\IScope $subScope)
    {
        return new self($subScope, $this->expressionProcessor);
    }

    public function buildScope()
    {
        return $this->scope->updateSegments($this->processSegments($this->scope->getSegments()));
    }

    protected function visitProjectionSegment(Segments\ProjectionSegment $segment)
    {
        return $segment->update($this->expressionProcessor->processFunction($segment->getProjectionFunction()));
    }

    public function visitIndexBy(Segments\IndexBy $segment)
    {
        return parent::visitIndexBy($this->visitProjectionSegment($segment));
    }

    public function visitSelect(Segments\Select $segment)
    {
        return parent::visitSelect($this->visitProjectionSegment($segment));
    }

    public function visitGroupBy(Segments\GroupBy $segment)
    {
        return parent::visitGroupBy($this->visitProjectionSegment($segment));
    }

    public function visitSelectMany(Segments\SelectMany $segment)
    {
        return parent::visitSelectMany($this->visitProjectionSegment($segment));
    }

    public function visitFilter(Segments\Filter $segment)
    {
        return parent::visitFilter($this->visitProjectionSegment($segment));
    }

    public function visitOrderBy(Segments\OrderBy $segment)
    {
        $orderings = [];

        foreach ($segment->getOrderings() as $key => $ordering) {
            $orderings[$key] = $ordering->update(
                    $this->expressionProcessor->processFunction($ordering->getProjectionFunction())
            );
        }

        return parent::visitOrderBy($segment->update($orderings));
    }

    public function visitJoin(Segments\Join $segment)
    {
        return parent::visitJoin($segment->update(
                $this->updateJoinOptions($segment->getOptions()),
                $this->expressionProcessor->processFunction($segment->getJoiningFunction())
        ));
    }

    public function updateJoinOptions(Queries\Common\Join\Options $options)
    {
        $filter = $options->getFilter();
        if ($filter instanceof Queries\Common\Join\Filter\Custom) {
            $filter = $filter->update($this->expressionProcessor->processFunction($filter->getOnFunction()));
        } elseif ($filter instanceof Queries\Common\Join\Filter\Equality) {
            $filter = $filter->update(
                    $this->expressionProcessor->processFunction($filter->getOuterKeyFunction()),
                    $this->expressionProcessor->processFunction($filter->getInnerKeyFunction())
            );
        }

        return $options->update(
                $options->getSource(),
                $options->isGroupJoin(),
                $filter,
                $options->hasDefault()
        );
    }
}
