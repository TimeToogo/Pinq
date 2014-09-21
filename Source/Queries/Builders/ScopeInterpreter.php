<?php

namespace Pinq\Queries\Builders;

use Pinq\Direction;
use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\PinqException;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\IScopeInterpretation;
use Pinq\Queries\Segments;
use Pinq\Utilities;

/**
 * Implementation of the scope expression interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeInterpreter extends ExpressionInterpreter implements IScopeInterpreter
{
    /**
     * @var IScopeInterpretation
     */
    protected $interpretation;

    /**
     * Because the method expression are evaluated top-down,
     * Query segments are interpreted in reverse order, so they are stored as
     * callbacks and called in reverse order.
     *
     * @var callable[]
     */
    protected $segmentCallbacks = [];

    /**
     * @var int
     */
    protected $segmentCounter = 0;

    /**
     * @var string
     */
    protected $segmentId = '';

    public function __construct(
            IScopeInterpretation $interpretation,
            O\IEvaluationContext $evaluationContext = null,
            $idPrefix = 'scope'
    ) {
        parent::__construct($idPrefix, $evaluationContext);
        $this->interpretation = $interpretation;
    }

    public function getInterpretation()
    {
        return $this->interpretation;
    }

    public function interpretScope(O\Expression $expression)
    {
        $this->segmentCallbacks = [];
        $this->visit($expression);

        foreach (array_reverse($this->segmentCallbacks) as $callback) {
            $callback();
        }
    }

    final protected function visit(O\Expression $expression)
    {
        if ($expression instanceof O\ValueExpression) {
            $queryable = $expression->getValue();
            if (!($queryable instanceof IQueryable)) {
                throw new PinqException('Invalid scope expression: must originate from %s, %s given',
                        IQueryable::IQUERYABLE_TYPE,
                        Utilities::getTypeOrClass($queryable));
            }

            if ($queryable->isSource()) {
                $this->addSegment(
                        function () use ($queryable) {
                            $this->interpretation->interpretScopeSource($queryable);
                        }
                );

                return;
            }

            $expression = $queryable->getExpression();
        }

        $methodName = $this->getMethodName($expression);
        $this->segmentCounter++;
        $this->segmentId = "{$this->segmentCounter}-{$methodName}";
        if (!method_exists($this, "visit$methodName")) {
            throw new PinqException('Cannot interpret query scope with method call \'%s\'', $methodName);
        }
        $this->{"visit$methodName"}($expression);
    }

    final protected function addSegment(callable $segmentCallback)
    {
        $segmentId                = $this->segmentId;
        $this->segmentCallbacks[] = function () use ($segmentCallback, $segmentId) {
            $segmentCallback($segmentId);
        };
    }

    final protected function visitWhere(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) use ($expression) {
                    $this->interpretation->interpretWhere(
                            $segmentId,
                            $this->getFunctionAt("$segmentId-predicate", 0, $expression)
                    );
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function getSegmentId($parameter = null)
    {
        return $parameter === null ? $this->segmentId : "{$this->segmentId}-$parameter";
    }

    final protected function visitOrderBy(O\MethodCallExpression $expression)
    {
        $this->visitOrderings($expression);
    }

    final protected function visitOrderings(O\MethodCallExpression $expression)
    {
        $orderings = [];

        $count = 0;
        while ($expression instanceof O\MethodCallExpression
                && stripos($methodName = $this->getMethodName($expression), 'thenBy') === 0) {

            $orderings[] = $this->visitOrdering($count, $expression);
            $expression  = $expression->getValue();
            $count++;
        }

        if (stripos($this->getMethodName($expression), 'orderBy') !== 0) {
            throw new PinqException(
                    'Cannot visit ordering query: must begin with an orderBy[Ascending|Descending] query method, %s given');
        }

        $orderings[] = $this->visitOrdering(++$count, $expression);

        $this->addSegment(
                function ($segmentId) use ($orderings) {
                    $this->interpretation->interpretOrderings($segmentId, array_reverse($orderings));
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function visitOrdering($count, O\MethodCallExpression $expression)
    {
        $projection = $this->getFunctionAt($this->getSegmentId("order-$count"), 0, $expression);

        $methodName = $this->getMethodName($expression);
        if (stripos($methodName, 'Ascending') !== false) {
            $isAscending = true;
        } elseif (stripos($methodName, 'Descending') !== false) {
            $isAscending = false;
        } else {
            $isAscending = $this->getArgumentValueAt(1, $expression) !== Direction::DESCENDING;
        }

        return [
                $projection,
                $this->getSegmentId("$count-isAscending"),
                $isAscending
        ];
    }

    final protected function visitOrderByAscending(O\MethodCallExpression $expression)
    {
        $this->visitOrderings($expression);
    }

    final protected function visitOrderByDescending(O\MethodCallExpression $expression)
    {
        $this->visitOrderings($expression);
    }

    final protected function visitThenBy(O\MethodCallExpression $expression)
    {
        $this->visitOrderings($expression);
    }

    final protected function visitThenByAscending(O\MethodCallExpression $expression)
    {
        $this->visitOrderings($expression);
    }

    final protected function visitThenByDescending(O\MethodCallExpression $expression)
    {
        $this->visitOrderings($expression);
    }

    final protected function visitSlice(O\MethodCallExpression $expression)
    {
        $this->addSlice($this->getArgumentValueAt(0, $expression), $this->getArgumentValueAt(1, $expression));

        $this->visit($expression->getValue());
    }

    final protected function addSlice($start, $amount)
    {
        $this->addSegment(
                function ($segmentId) use ($start, $amount) {
                    $this->interpretation->interpretSlice(
                            $segmentId,
                            "$segmentId-start",
                            $start,
                            "$segmentId-amount",
                            $amount
                    );
                }
        );
    }

    final protected function visitSkip(O\MethodCallExpression $expression)
    {
        $this->addSlice($this->getArgumentValueAt(0, $expression), null);

        $this->visit($expression->getValue());
    }

    final protected function visitTake(O\MethodCallExpression $expression)
    {
        $this->addSlice(0, $this->getArgumentValueAt(0, $expression));

        $this->visit($expression->getValue());
    }

    final protected function visitIndexBy(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) use ($expression) {
                    $this->interpretation->interpretIndexBy(
                            $segmentId,
                            $this->getFunctionAt("$segmentId-projection", 0, $expression)
                    );
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function visitKeys(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) {
                    $this->interpretation->interpretKeys($segmentId);
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function visitReindex(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) {
                    $this->interpretation->interpretReindex($segmentId);
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function visitGroupBy(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) use ($expression) {
                    $this->interpretation->interpretGroupBy(
                            $segmentId,
                            $this->getFunctionAt("$segmentId-projection", 0, $expression)
                    );
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function visitTo(O\MethodCallExpression $expression)
    {
        $joinToFunction = $this->getFunctionAt($this->getSegmentId('projection'), 0, $expression);
        $expression     = $this->getSourceMethodCall($expression);

        $optionsInterpreter = $this->buildJoinOptionsInterpreter($this->segmentId);
        $optionsInterpreter->interpretJoinOptions(
                $expression,
                $sourceExpression
        );

        $this->addSegment(
                function ($segmentId) use ($optionsInterpreter, $joinToFunction) {
                    $this->interpretation->interpretJoin(
                            $segmentId,
                            $optionsInterpreter->getInterpretation(),
                            $joinToFunction
                    );
                }
        );
        $this->visit($sourceExpression->getValue());
    }

    public function buildJoinOptionsInterpreter($segmentId)
    {
        return new JoinOptionsInterpreter(
                $segmentId,
                $this->interpretation->buildJoinOptionsInterpretation(),
                $this->buildSourceInterpreter($segmentId),
                $this->evaluationContext
        );
    }

    public function buildSourceInterpreter($segmentId)
    {
        return new SourceInterpreter(
                $segmentId,
                $this->interpretation->buildSourceInterpretation(),
                new static(
                        $this->interpretation->buildScopeInterpretation(),
                        $this->evaluationContext,
                        "$segmentId-scope"),
            $this->evaluationContext
        );
    }

    final protected function visitSelect(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) use ($expression) {
                    $this->interpretation->interpretSelect(
                            $segmentId,
                            $this->getFunctionAt("$segmentId-projection", 0, $expression)
                    );
                }
        );
        $this->visit($expression->getValue());
    }

    final protected function visitSelectMany(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) use ($expression) {
                    $this->interpretation->interpretSelectMany(
                            $segmentId,
                            $this->getFunctionAt("$segmentId-projection", 0, $expression)
                    );
                }
        );
        $this->visit($expression->getValue());
    }

    final protected function visitUnique(O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) {
                    $this->interpretation->interpretUnique($segmentId);
                }
        );
        $this->visit($expression->getValue());
    }

    final protected function visitAppend(O\MethodCallExpression $expression)
    {
        $this->visitOperation(Segments\Operation::APPEND, $expression);
    }

    final protected function visitOperation($operationType, O\MethodCallExpression $expression)
    {
        $this->addSegment(
                function ($segmentId) use ($operationType, $expression) {
                    $sourceInterpreter = $this->buildSourceInterpreter($segmentId);
                    $sourceInterpreter->interpretSource($this->getArgumentAt(0, $expression));

                    $this->interpretation->interpretOperation(
                            $this->getSegmentId($operationType),
                            $operationType,
                            $sourceInterpreter->getInterpretation()
                    );
                }
        );

        $this->visit($expression->getValue());
    }

    final protected function visitWhereIn(O\MethodCallExpression $expression)
    {
        $this->visitOperation(Segments\Operation::WHERE_IN, $expression);
    }

    final protected function visitExcept(O\MethodCallExpression $expression)
    {
        $this->visitOperation(Segments\Operation::EXCEPT, $expression);
    }

    final protected function visitUnion(O\MethodCallExpression $expression)
    {
        $this->visitOperation(Segments\Operation::UNION, $expression);
    }

    final protected function visitIntersect(O\MethodCallExpression $expression)
    {
        $this->visitOperation(Segments\Operation::INTERSECT, $expression);
    }

    final protected function visitDifference(O\MethodCallExpression $expression)
    {
        $this->visitOperation(Segments\Operation::DIFFERENCE, $expression);
    }
}
