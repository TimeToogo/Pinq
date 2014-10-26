<?php

namespace Pinq\Providers\Traversable;

use Pinq\Direction;
use Pinq\ITraversable;
use Pinq\PinqException;
use Pinq\Queries;
use Pinq\Queries\Common;
use Pinq\Queries\Segments;

/**
 * Evaluates the query scope against the supplied traversable
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeEvaluator extends Segments\SegmentVisitor
{
    /**
     * @var ITraversable
     */
    protected $traversable;

    /**
     * @var Queries\IResolvedParameterRegistry
     */
    protected $resolvedParameters;

    private function __construct(
            ITraversable $traversable,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $this->traversable         = $traversable;
        $this->resolvedParameters  = $resolvedParameters;
    }

    public function visitFilter(Segments\Filter $query)
    {
        $this->traversable = $this->traversable->where(
                $this->resolvedParameters[$query->getProjectionFunction()->getCallableId()]
        );
    }

    public function visitRange(Segments\Range $query)
    {
        $this->traversable =
                $this->traversable->slice(
                        $this->resolvedParameters[$query->getStartId()],
                        $this->resolvedParameters[$query->getAmountId()]
                );
    }

    public function visitSelect(Segments\Select $query)
    {
        $this->traversable = $this->traversable->select(
                $this->resolvedParameters[$query->getProjectionFunction()->getCallableId()]
        );
    }

    public function visitSelectMany(Segments\SelectMany $query)
    {
        $this->traversable = $this->traversable->selectMany(
                $this->resolvedParameters[$query->getProjectionFunction()->getCallableId()]
        );
    }

    public function visitUnique(Segments\Unique $query)
    {
        $this->traversable = $this->traversable->unique();
    }

    public function visitOrderBy(Segments\OrderBy $query)
    {
        $first = true;

        foreach ($query->getOrderings() as $orderFunction) {
            $direction = $this->resolvedParameters[$orderFunction->getIsAscendingId(
            )] ? Direction::ASCENDING : Direction::DESCENDING;

            if ($first) {
                $this->traversable = $this->traversable->orderBy(
                        $this->resolvedParameters[$orderFunction->getProjectionFunction()->getCallableId()],
                        $direction
                );
                $first             = false;
            } else {
                $this->traversable = $this->traversable->thenBy(
                        $this->resolvedParameters[$orderFunction->getProjectionFunction()->getCallableId()],
                        $direction
                );
            }
        }
    }

    public function visitGroupBy(Segments\GroupBy $query)
    {
        $this->traversable = $this->traversable->groupBy(
                $this->resolvedParameters[$query->getProjectionFunction()->getCallableId()]
        );
    }

    public function visitJoin(Segments\Join $query)
    {
        $this->traversable = self::evaluateJoinOptions(
                $this->traversable,
                $query->getOptions(),
                $this->resolvedParameters
        )
                ->to($this->resolvedParameters[$query->getJoiningFunction()->getCallableId()]);
    }

    /**
     * Evaluates the join segment values and filter upon the supplied traversable.
     *
     * @param ITraversable                       $traversable
     * @param Common\Join\Options                $join
     * @param Queries\IResolvedParameterRegistry $resolvedParameters
     *
     * @return \Pinq\Interfaces\IJoiningToTraversable
     */
    public static function evaluateJoinOptions(
            ITraversable $traversable,
            Common\Join\Options $join,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $values             = self::evaluateSource($join->getSource(), $resolvedParameters);
        $joiningTraversable = $join->isGroupJoin() ? $traversable->groupJoin($values) : $traversable->join($values);

        if ($join->hasFilter()) {
            $filter = $join->getFilter();

            if ($filter instanceof Common\Join\Filter\Custom) {
                $joiningTraversable = $joiningTraversable->on(
                        $resolvedParameters[$filter->getOnFunction()->getCallableId()]
                );
            } elseif ($filter instanceof Common\Join\Filter\Equality) {
                $joiningTraversable = $joiningTraversable->onEquality(
                        $resolvedParameters[$filter->getOuterKeyFunction()->getCallableId()],
                        $resolvedParameters[$filter->getInnerKeyFunction()->getCallableId()]
                );
            }
        }

        if ($join->hasDefault()) {
            $joiningTraversable = $joiningTraversable->withDefault(
                    $resolvedParameters[$join->getDefaultValueId()],
                    $resolvedParameters[$join->getDefaultKeyId()]
            );
        }

        return $joiningTraversable;
    }

    public static function evaluateSource(
            Common\ISource $source,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        if ($source instanceof Common\Source\ArrayOrIterator) {
            return $resolvedParameters[$source->getId()];
        } elseif ($source instanceof Common\Source\SingleValue) {
            return [$resolvedParameters[$source->getId()]];
        } elseif ($source instanceof Common\Source\QueryScope) {
            return self::evaluate($source->getScope(), $resolvedParameters);
        }
    }

    public static function evaluate(
            Queries\IScope $scope,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $sourceInfo = $scope->getSourceInfo();
        if (!($sourceInfo instanceof SourceInfo)) {
            throw new PinqException(
                    'Incompatible query source: expecting source info of type %s, %s given',
                    SourceInfo::SOURCE_INFO_TYPE,
                    get_class($sourceInfo));
        }

        $evaluator = new self($sourceInfo->getTraversable(), $resolvedParameters);
        $evaluator->visit($scope);

        return $evaluator->traversable;
    }

    public function visitIndexBy(Segments\IndexBy $query)
    {
        $this->traversable = $this->traversable->indexBy(
                $this->resolvedParameters[$query->getProjectionFunction()->getCallableId()]
        );
    }

    public function visitKeys(Segments\Keys $query)
    {
        $this->traversable = $this->traversable->keys();
    }

    public function visitReindex(Segments\Reindex $query)
    {
        $this->traversable = $this->traversable->reindex();
    }

    public function visitOperation(Segments\Operation $query)
    {
        $otherValues = self::evaluateSource($query->getSource(), $this->resolvedParameters);
        switch ($query->getOperationType()) {

            case Segments\Operation::UNION:
                $this->traversable = $this->traversable->union($otherValues);
                break;

            case Segments\Operation::INTERSECT:
                $this->traversable = $this->traversable->intersect($otherValues);
                break;

            case Segments\Operation::DIFFERENCE:
                $this->traversable = $this->traversable->difference($otherValues);
                break;

            case Segments\Operation::APPEND:
                $this->traversable = $this->traversable->append($otherValues);
                break;

            case Segments\Operation::WHERE_IN:
                $this->traversable = $this->traversable->whereIn($otherValues);
                break;

            case Segments\Operation::EXCEPT:
                $this->traversable = $this->traversable->except($otherValues);
                break;
        }
    }
}
