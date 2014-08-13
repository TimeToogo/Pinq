<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Direction;
use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\IRepository;
use Pinq\Providers;
use Pinq\Parsing;
use Pinq\Queries as Q;

abstract class QueryBuildingTest extends \Pinq\Tests\PinqTestCase
{
    const SCOPE_TYPE = __CLASS__;
    const SCOPE_NAMESPACE = __NAMESPACE__;

    /**
     * @var Parsing\IFunctionInterpreter
     */
    protected $functionInterpreter;

    /**
     * @var IQueryable
     */
    protected $queryable;

    /**
     * @var IRepository
     */
    protected $repository;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->queryable = isset($data[0]) ? $data[0] : null;
        $this->repository = $this->queryable instanceof IRepository ? $this->queryable : null;
    }


    protected function setUp()
    {
        $this->functionInterpreter = Parsing\FunctionInterpreter::getDefault();
    }

    /**
     * @return Providers\IQueryProvider[]
     */
    abstract public function queryProviders();

    /**
     * @return Providers\IRepositoryProvider[]
     */
    abstract public function repositoryProviders();

    final public function queryables()
    {
        $queryables = [];
        foreach ($this->queryProviders() as $provider) {
            $queryables[] = [$provider->createQueryable()];
        }

        return $queryables;
    }

    final public function repositories()
    {
        $repositories = [];
        foreach ($this->repositoryProviders() as $provider) {
            $repositories[] = [$provider->createRepository()];
        }

        return $repositories;
    }

    final public function allImplementations()
    {
        return array_merge($this->queryables(), $this->repositories());
    }

    final protected function assertRequestIsCorrect(callable $requestFunction, $correctValue, $onlyAsParsedExpression = false)
    {
        $requestBuilder = $this->queryable->getProvider()->getConfiguration()->getRequestQueryBuilder();

        if(!$onlyAsParsedExpression) {
            $request = $requestBuilder->parseRequest($requestFunction($this->queryable)->getExpression());
            $this->assertRequestQueryMatches($request, $correctValue);
        }

        $request = $requestBuilder->parseRequest($this->parseQueryExpression($requestFunction, $evaluationContext), $evaluationContext);
        $this->assertRequestQueryMatches($request, $correctValue);
    }

    abstract protected function assertRequestQueryMatches(Q\IRequestQuery $requestQuery, $correctValue);

    final protected function assertOperationIsCorrect(callable $operationFunction, $correctValue)
    {
        $operationBuilder = $this->repository->getProvider()->getConfiguration()->getOperationQueryBuilder();

        $operation = $operationBuilder->parseOperation($this->parseQueryExpression($operationFunction, $evaluationContext), $evaluationContext);
        $this->assertOperationQueryMatches($operation, $correctValue);
    }

    abstract protected function assertOperationQueryMatches(Q\IOperationQuery $operationQuery, $correctValue);

    protected function parseQueryExpression(callable $queryFunction, O\IEvaluationContext &$evaluationContext = null)
    {
        $reflection        = $this->functionInterpreter->getReflection($queryFunction);
        $evaluationContext = $reflection->asEvaluationContext();
        $function          = $this->functionInterpreter->getStructure($reflection);
        $expressions       = $function->getBodyExpressions();
        $this->assertCount(1, $expressions);

        //Resolve the parameter variable with the queryable value and $this
        $parameterName = $reflection->getSignature()->getParameterExpressions()[0]->getName();

        $expression =  $expressions[0];
        foreach([$parameterName => $this->queryable, 'this' => $this] as $variable => $value) {
            $variableReplacer = new O\DynamicExpressionWalker([
                    O\VariableExpression::getType() => function (O\VariableExpression $expression) use ($variable, &$value) {
                                if($expression->getName() instanceof O\ValueExpression
                                        && $expression->getName()->getValue() === $variable) {
                                    return O\Expression::value($value);
                                } else {
                                    return $expression;
                                }
                            },
                    //Ignore closures
                    O\ClosureExpression::getType() => function ($closure) { return $closure; }
            ]);

            $expression = $variableReplacer->walk($expression);
        }

        if($expression instanceof O\ReturnExpression) {
            return $expression->getValue();
        } else {
            return $expression;
        }
    }

    /**
     * @dataProvider allImplementations
     */
    public function testValuesAsSelf()
    {
        $this->assertRequestIsCorrect(
                function (\Pinq\IQueryable $queryable) {
                    return $queryable;
                },
                $this->valuesAsSelfQuery()
        );
    }

    abstract protected function valuesAsSelfQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testValuesAsArray()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->asArray();
                },
                $this->valuesAsArrayQuery(),
                true
        );
    }

    abstract protected function valuesAsArrayQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testValuesAsIterator()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->getIterator();
                },
                $this->valuesAsIteratorQuery(),
                true
        );
    }

    abstract protected function valuesAsIteratorQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testValuesAsTrueIterator()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->getTrueIterator();
                },
                $this->valuesAsTrueIteratorQuery(),
                true
        );
    }

    abstract protected function valuesAsTrueIteratorQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testValuesAsCollection()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->asCollection();
                },
                $this->valuesAsCollectionQuery(),
                true
        );
    }

    abstract protected function valuesAsCollectionQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testValuesAsTraversable()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->asTraversable();
                },
                $this->valuesAsTraversableQuery(),
                true
        );
    }

    abstract protected function valuesAsTraversableQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testOffsetGetQuery()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable[0];
                },
                $this->offsetGetQuery(),
                true
        );
    }

    abstract protected function offsetGetQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testOffsetIsset()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    isset($queryable[0]);
                },
                $this->offsetIssetQuery(),
                true
        );
    }

    abstract protected function offsetIssetQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testIsEmpty()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->isEmpty();
                },
                $this->isEmptyQuery(),
                true
        );
    }

    abstract protected function isEmptyQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testCount()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->count();
                },
                $this->countQuery(),
                true
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    count($queryable);
                },
                $this->countQuery(),
                true
        );
    }

    abstract protected function countQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testFirst()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->first();
                },
                $this->firstQuery(),
                true
        );
    }

    abstract protected function firstQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testLast()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->last();
                },
                $this->lastQuery(),
                true
        );
    }

    abstract protected function lastQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testAggregate()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->aggregate(function ($a, $s) { return $a + $s; });
                },
                $this->aggregateQuery(),
                true
        );
    }

    abstract protected function aggregateQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testAll()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->all();
                },
                $this->allQuery(),
                true
        );
    }

    abstract protected function allQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testAny()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->any();
                },
                $this->anyQuery(),
                true
        );
    }

    abstract protected function anyQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testMaximum()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->maximum();
                },
                $this->maximumQuery(),
                true
        );
    }

    abstract protected function maximumQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testMinimum()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->minimum();
                },
                $this->minimumQuery(),
                true
        );
    }

    abstract protected function minimumQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testSum()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->sum();
                },
                $this->sumQuery(),
                true
        );
    }

    abstract protected function sumQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testAverage()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->average();
                },
                $this->averageQuery(),
                true
        );
    }

    abstract protected function averageQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testImplode()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->implode('');
                },
                $this->implodeQuery(),
                true
        );
    }

    abstract protected function implodeQuery();

    /**
     * @dataProvider repositories
     */
    public function testApply()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->apply(function (&$i) { $i *= 10; });
                },
                $this->applyQuery()
        );
    }

    abstract protected function applyQuery();

    /**
     * @dataProvider repositories
     */
    public function testAddRange()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->addRange([]);
                },
                $this->addRangeQuery()
        );
    }

    abstract protected function addRangeQuery();

    /**
     * @dataProvider repositories
     */
    public function testRemoveRange()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->removeRange([]);
                },
                $this->removeRangeQuery()
        );
    }

    abstract protected function removeRangeQuery();

    /**
     * @dataProvider repositories
     */
    public function testRemoveWhere()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->removeWhere(function () { return true; });
                },
                $this->removeWhereQuery()
        );
    }

    abstract protected function removeWhereQuery();

    /**
     * @dataProvider repositories
     */
    public function testRemove()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->remove(null);
                },
                $this->removeQuery()
        );
    }

    abstract protected function removeQuery();

    /**
     * @dataProvider repositories
     */
    public function testClear()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->clear();
                },
                $this->clearQuery()
        );
    }

    abstract protected function clearQuery();

    /**
     * @dataProvider repositories
     */
    public function testJoinApply()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository
                            ->join([])
                            ->on(function () { return true; })
                            ->apply(function (&$o, $i) { $o *= $i; });
                },
                $this->joinApplyQuery()
        );
    }

    abstract protected function joinApplyQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testWhere()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->where(function () { return true; });
                },
                $this->whereQuery()
        );
    }

    abstract protected function whereQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testOrderByAscending()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->orderBy(function ($i) { return $i; }, Direction::ASCENDING);
                },
                $this->orderByAscendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->orderBy(function ($i) { return $i; }, SORT_ASC);
                },
                $this->orderByAscendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->orderByAscending(function ($i) { return $i; });
                },
                $this->orderByAscendingQuery()
        );
    }

    abstract protected function orderByAscendingQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testOrderByDescending()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->orderBy(function ($i) { return $i; }, Direction::DESCENDING);
                },
                $this->orderByAscendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->orderBy(function ($i) { return $i; }, SORT_DESC);
                },
                $this->orderByAscendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->orderByDescending(function ($i) { return $i; });
                },
                $this->orderByDescendingQuery()
        );
    }

    abstract protected function orderByDescendingQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testOrderByAscendingWithThenByAscending()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->orderByAscending(function ($i) { return $i; })
                            ->thenBy(function ($i) { return $i; }, Direction::ASCENDING);
                },
                $this->orderByAscendingWithThenByAscendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->orderByAscending(function ($i) { return $i; })
                            ->thenBy(function ($i) { return $i; }, SORT_ASC);
                },
                $this->orderByAscendingWithThenByAscendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->orderByAscending(function ($i) { return $i; })
                            ->thenByAscending(function ($i) { return $i; });
                },
                $this->orderByAscendingWithThenByAscendingQuery()
        );
    }

    abstract protected function orderByAscendingWithThenByAscendingQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testOrderByAscendingWithThenByDescending()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->orderByAscending(function ($i) { return $i; })
                            ->thenBy(function ($i) { return $i; }, Direction::DESCENDING);
                },
                $this->orderByAscendingWithThenByDescendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->orderByAscending(function ($i) { return $i; })
                            ->thenBy(function ($i) { return $i; }, SORT_DESC);
                },
                $this->orderByAscendingWithThenByDescendingQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->orderByAscending(function ($i) { return $i; })
                            ->thenByDescending(function ($i) { return $i; });
                },
                $this->orderByAscendingWithThenByDescendingQuery()
        );
    }

    abstract protected function orderByAscendingWithThenByDescendingQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testSkip()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->skip(1);
                },
                $this->skipQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->slice(1, null);
                },
                $this->skipQuery()
        );
    }

    abstract protected function skipQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testTake()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->take(1);
                },
                $this->takeQuery()
        );

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->slice(0, 1);
                },
                $this->takeQuery()
        );
    }

    abstract protected function takeQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testIndexBy()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->indexBy(function ($i) { return $i; });
                },
                $this->indexByQuery()
        );
    }

    abstract protected function indexByQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testKeys()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->keys();
                },
                $this->keysQuery()
        );
    }

    abstract protected function keysQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testReindex()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->reindex();
                },
                $this->reindexQuery()
        );
    }

    abstract protected function reindexQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testGroupBy()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->groupBy(function ($i) { return $i; });
                },
                $this->groupByQuery()
        );
    }

    abstract protected function groupByQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testUnfilteredJoin()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->join([])
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->unfilteredJoinQuery()
        );
    }

    abstract protected function unfilteredJoinQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testJoinOn()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->join([])
                            ->on(function ($o, $i) { return $o === $i; })
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->joinOnQuery()
        );
    }

    abstract protected function joinOnQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testJoinOnEquality()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->join([])
                            ->onEquality(
                                    function ($o) { return $o; },
                                    function ($i) { return $i; })
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->joinOnEqualityQuery()
        );
    }

    abstract protected function joinOnEqualityQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testJoinWithDefault()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->join([])
                            ->withDefault('v', 'k')
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->joinWithDefaultQuery()
        );
    }

    abstract protected function joinWithDefaultQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testJoinTwoWithDefaults()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->join([])
                            ->withDefault('v1', 'k1')
                            ->withDefault('v2', 'k2')//should use last
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->joinTwoWithDefaultsQuery()
        );
    }

    abstract protected function joinTwoWithDefaultsQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testJoinToSubScope()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->join($queryable
                                    ->orderByAscending(function ($i) { return $i; })
                                    ->select(function ($i) { return $i; })
                                    ->take(50))
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->joinToSubScopeQuery()
        );
    }

    abstract protected function joinToSubScopeQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testUnfilteredGroupJoinWithDefault()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->groupJoin([])
                            ->to(function ($o, $i) { return [$o, $i]; });
                },
                $this->unfilteredGroupJoinWithDefaultQuery()
        );
    }

    abstract protected function unfilteredGroupJoinWithDefaultQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testUnique()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->unique();
                },
                $this->uniqueQuery()
        );
    }

    abstract protected function uniqueQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testSelect()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->select(function ($i) { return $i; });
                },
                $this->selectQuery()
        );
    }

    abstract protected function selectQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testSelectMany()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->selectMany(function ($i) { return $i; });
                },
                $this->selectManyQuery()
        );
    }

    abstract protected function selectManyQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testAppend()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->append([]);
                },
                $this->appendQuery()
        );
    }

    abstract protected function appendQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testWhereIn()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->whereIn([]);
                },
                $this->whereInQuery()
        );
    }

    abstract protected function whereInQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testExcept()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->except([]);
                },
                $this->exceptQuery()
        );
    }

    abstract protected function exceptQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testUnion()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->union([]);
                },
                $this->unionQuery()
        );
    }

    abstract protected function unionQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testIntersect()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->intersect([]);
                },
                $this->intersectQuery()
        );
    }

    abstract protected function intersectQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testDifference()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->difference([]);
                },
                $this->differenceQuery()
        );
    }

    abstract protected function differenceQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testIntersectWithFilteredSubScope()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->intersect($queryable->where(function () { return true; }));
                },
                $this->intersectWithFilteredSubScopeQuery()
        );
    }

    abstract protected function intersectWithFilteredSubScopeQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testNestedOperations()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->append(
                            $queryable->whereIn(
                                $queryable->except(
                                        $queryable->union(
                                                $queryable->intersect(
                                                        $queryable->difference(
                                                                $queryable->take(5)
                                                        )
                                                )
                                        )
                                )
                            )
                    );
                },
                $this->nestedOperationsQuery()
        );
    }

    abstract protected function nestedOperationsQuery();

    /**
     * @dataProvider allImplementations
     */
    public function testExampleFromDocs()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->where(function ($row) { return $row['age'] <= 50; })
                            ->orderByAscending(function ($row) { return $row['firstName']; })
                            ->thenByAscending(function ($row) { return $row['lastName']; })
                            ->take(50)
                            ->indexBy(function ($row) { return $row['phoneNumber']; })
                            ->select(function ($row) {
                                return [
                                        'fullName' => $row['firstName'] . ' ' . $row['lastName'],
                                        'address' => $row['address'],
                                        'dateOfBirth' => $row['dateOfBirth'],
                                ];
                            });
                },
                $this->exampleFromDocsQuery()
        );
    }

    abstract protected function exampleFromDocsQuery();
}
