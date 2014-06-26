<?php

namespace Pinq\Providers\Traversable;

use Pinq\Queries\Common;
use Pinq\Queries\Segments;

/**
 * Evaluates the query scope against the supplied traversable
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ScopeEvaluator extends Segments\SegmentVisitor
{
    /**
     * @var \Pinq\ITraversable
     */
    protected $traversable;
    
    public function __construct(\Pinq\ITraversable $traversable = null)
    {
        $this->traversable = $traversable;
    }
    
    final public function setTraversable(\Pinq\ITraversable $traversable)
    {
        $this->traversable = $traversable;
    }
    
    public static function evaluate(\Pinq\ITraversable $traversable, \Pinq\Queries\IScope $scope)
    {
        $evaluator = new self($traversable);
        $evaluator->walk($scope);
        
        return $evaluator->traversable;
    }
    
    /**
     * @return \Pinq\ITraversable
     */
    public function getTraversable()
    {
        return $this->traversable;
    }

    public function visitFilter(Segments\Filter $query)
    {
        $this->traversable = $this->traversable->where($query->getFunctionExpressionTree());
    }

    public function visitRange(Segments\Range $query)
    {
        $this->traversable =
                $this->traversable->slice(
                        $query->getRangeStart(),
                        $query->getRangeAmount());
    }

    public function visitSelect(Segments\Select $query)
    {
        $this->traversable = $this->traversable->select($query->getFunctionExpressionTree());
    }

    public function visitSelectMany(Segments\SelectMany $query)
    {
        $this->traversable = $this->traversable->selectMany($query->getFunctionExpressionTree());
    }

    public function visitUnique(Segments\Unique $query)
    {
        $this->traversable = $this->traversable->unique();
    }

    public function visitOrderBy(Segments\OrderBy $query)
    {
        $first = true;

        foreach ($query->getOrderFunctions() as $orderFunction) {
            $direction = $orderFunction->isAscending() ? \Pinq\Direction::ASCENDING : \Pinq\Direction::DESCENDING;

            if ($first) {
                $this->traversable = $this->traversable->orderBy($orderFunction->getFunctionExpressionTree(), $direction);
                $first = false;
            } else {
                $this->traversable = $this->traversable->thenBy($orderFunction->getFunctionExpressionTree(), $direction);
            }
        }
    }

    public function visitGroupBy(Segments\GroupBy $query)
    {
        $first = true;

        foreach ($query->getFunctionExpressionTrees() as $functionExpressionTree) {
            $this->traversable = $first ? $this->traversable->groupBy($functionExpressionTree) : $this->traversable->andBy($functionExpressionTree);

            if ($first) {
                $first = false;
            }
        }
    }
    
    /**
     * Evaluates the join segment values and filter upon the supplied traversable.
     * 
     * @param \Pinq\ITraversable $traversable
     * @param Common\Join\Base $join
     * @return \Pinq\Interfaces\IJoiningToTraversable
     */
    public static function evaluateJoin(\Pinq\ITraversable $traversable, Common\Join\Base $join)
    {
        $joiningTraversable = $join->isGroupJoin() ? 
                $traversable->groupJoin($join->getValues()) : $traversable->join($join->getValues());
        
        if($join->hasFilter()) {
            $filter = $join->getFilter();
            
            if($filter instanceof Common\Join\Filter\On) {
                $joiningTraversable = $joiningTraversable->on($filter->getOnFunctionExpressionTree());
            } elseif ($filter instanceof Common\Join\Filter\Equality) {
                $joiningTraversable = $joiningTraversable->onEquality(
                        $filter->getOuterKeyFunctionExpressionTree(), 
                        $filter->getInnerKeyFunctionExpressionTree());
            }
        }
        
        return $joiningTraversable;
    }

    public function visitJoin(Segments\Join $query)
    {
        $this->traversable = self::evaluateJoin($this->traversable, $query)
                ->to($query->getJoiningFunctionExpressionTree());
    }

    protected function visitIndexBy(Segments\IndexBy $query)
    {
        $this->traversable = $this->traversable->indexBy($query->getFunctionExpressionTree());
    }
    
    protected function visitKeys(Segments\Keys $query)
    {
        $this->traversable = $this->traversable->keys();
    }
    
    protected function visitReindex(Segments\Reindex $query)
    {
        $this->traversable = $this->traversable->reindex();
    }

    public function visitOperation(Segments\Operation $query)
    {
        $otherValues = $query->getValues();
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
