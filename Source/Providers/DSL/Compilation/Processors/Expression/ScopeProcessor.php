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

    public function __construct(IExpressionProcessor $expressionProcessor)
    {
        parent::__construct($expressionProcessor->getScope());

        $this->expressionProcessor = $expressionProcessor;
    }

    public function buildScope()
    {
        $this->processSegments($this->scope->getSegments());
        return $this->expressionProcessor->buildScope();
    }

    /**
     * @param Queries\Common\ISource $source
     *
     * @return Queries\Common\ISource
     */
    public function processSource(Queries\Common\ISource $source)
    {
        if ($source instanceof Queries\Common\Source\QueryScope) {
            $processor = new self($this->expressionProcessor->forSubScope($source->getScope()));
            return $source->update($processor->buildScope());
        }

        return $source;
    }

    protected function visitProjectionSegment(Segments\ProjectionSegment $segment)
    {
        return $segment->update($this->expressionProcessor->processFunction($segment->getProjectionFunction()));
    }

    public function visitIndexBy(Segments\IndexBy $segment)
    {
        $this->expressionProcessor->addSegment($this->visitProjectionSegment($segment));
    }

    public function visitSelect(Segments\Select $segment)
    {
        $this->expressionProcessor->addSegment($this->visitProjectionSegment($segment));
    }

    public function visitGroupBy(Segments\GroupBy $segment)
    {
        $this->expressionProcessor->addSegment($this->visitProjectionSegment($segment));
    }

    public function visitSelectMany(Segments\SelectMany $segment)
    {
        $this->expressionProcessor->addSegment($this->visitProjectionSegment($segment));
    }

    public function visitFilter(Segments\Filter $segment)
    {
        $this->expressionProcessor->addSegment($this->visitProjectionSegment($segment));
    }

    public function visitOrderBy(Segments\OrderBy $segment)
    {
        $orderings = [];
        foreach ($segment->getOrderings() as $key => $ordering) {
            $orderings[$key] = $ordering->update(
                    $this->expressionProcessor->processFunction($ordering->getProjectionFunction())
            );
        }
        $this->expressionProcessor->addSegment($segment->update($orderings));
    }

    public function visitJoin(Segments\Join $segment)
    {
        $this->expressionProcessor->addSegment(
                $segment->update(
                        $this->updateJoinOptions($segment->getOptions()),
                        $this->expressionProcessor->processFunction($segment->getJoiningFunction())
                )
        );
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
                $this->processSource($options->getSource()),
                $options->isGroupJoin(),
                $filter,
                $options->hasDefault()
        );
    }

    public function visitOperation(Segments\Operation $segment)
    {
        $this->expressionProcessor->addSegment($segment->updateSource($this->processSource($segment->getSource())));
    }

    public function visitKeys(Segments\Keys $segment)
    {
        $this->expressionProcessor->addSegment($segment);
    }

    public function visitRange(Segments\Range $segment)
    {
        $this->expressionProcessor->addSegment($segment);
    }

    public function visitUnique(Segments\Unique $segment)
    {
        $this->expressionProcessor->addSegment($segment);
    }

    public function visitReindex(Segments\Reindex $segment)
    {
        $this->expressionProcessor->addSegment($segment);
    }
}