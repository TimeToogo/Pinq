<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\ICollection;
use Pinq\Collection;
use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\Parsing;
use Pinq\Providers;
use Pinq\Queries as Q;
use Pinq\Traversable;

class IgnoreParametersComparator extends \PHPUnit_Framework_Comparator
{


    public function accepts($expected, $actual)
    {
        return (is_string($expected) && is_string($actual) && QueryParsingTest::isParameter($expected))
        || ($expected instanceof Q\IParameterRegistry && $actual instanceof Q\IParameterRegistry);
    }

    public function assertEquals($expected, $actual, $delta = 0, $canonicalize = false, $ignoreCase = false)
    {
        return true;
    }
}

class IgnoreParametersInArrayKeysComparator extends \PHPUnit_Framework_Comparator_Array
{
    public function assertEquals($expected, $actual, $delta = 0, $canonicalize = false, $ignoreCase = false)
    {
        foreach($expected as $key => $value) {
            if(!QueryParsingTest::isParameter($key)) {
                parent::assertEquals($expected, $actual, $delta, $canonicalize, $ignoreCase);
                return;
            }
        }

        parent::assertEquals(array_values($expected), array_values($actual), $delta, $canonicalize, $ignoreCase);
    }
}

class QueryParsingTest extends QueryBuildingTest
{
    /**
     * @var ICollection
     */
    protected $collection;


    const PARAMETER_NAME = '~~~PARAMETER~~~';
    private static $number = 0;

    public function parameter()
    {
        return self::PARAMETER_NAME . self::$number++;
    }

    public static function isParameter($value)
    {
        return is_string($value) && strpos($value, self::PARAMETER_NAME) === 0;
    }

    protected function scope(array $segments)
    {
        return new Q\Scope(
                $this->queryable->getSourceInfo(),
                $segments);
    }

    protected function scopeSource(array $segments)
    {
        return new Q\Common\Source\QueryScope($this->scope($segments));
    }

    protected function request(array $segments, Q\IRequest $request)
    {
        return new Q\RequestQuery(
                $this->scope($segments),
                $request,
                new Q\ParameterRegistry([]));
    }

    protected function scopeRequest(array $segments)
    {
        return $this->request($segments, new Q\Requests\Values(Q\Requests\Values::AS_SELF));
    }

    protected function operation(array $segments, Q\IOperation $operation)
    {
        return new Q\OperationQuery(
                $this->scope($segments),
                $operation,
                new Q\ParameterRegistry([]));
    }

    /**
     * @return Providers\IQueryProvider[]
     */
    public function queryProviders()
    {
        return [new Providers\Traversable\Provider(new Traversable())];
    }

    /**
     * @return Providers\IRepositoryProvider[]
     */
    public function repositoryProviders()
    {
        return [new Providers\Collection\Provider(new Collection())];
    }

    private static $ignoreParametersComparator;
    private static $ignoreParametersInArrayKeysComparator;

    public static function setUpBeforeClass()
    {
        self::$ignoreParametersComparator = new IgnoreParametersComparator();
        self::$ignoreParametersInArrayKeysComparator = new IgnoreParametersInArrayKeysComparator();
        \PHPUnit_Framework_ComparatorFactory::getDefaultInstance()->register(self::$ignoreParametersComparator);
        \PHPUnit_Framework_ComparatorFactory::getDefaultInstance()->register(self::$ignoreParametersInArrayKeysComparator);
    }

    public static function tearDownAfterClass()
    {
        \PHPUnit_Framework_ComparatorFactory::getDefaultInstance()->unregister(self::$ignoreParametersComparator);
        \PHPUnit_Framework_ComparatorFactory::getDefaultInstance()->unregister(self::$ignoreParametersInArrayKeysComparator);
    }


    protected function assertRequestQueryMatches(Q\IRequestQuery $requestQuery, $correctValue)
    {
        /** @var $correctValue Q\IRequestQuery */
        $this->assertEquals($correctValue, $requestQuery);
    }

    protected function assertOperationQueryMatches(Q\IOperationQuery $operationQuery, $correctValue)
    {
        /** @var $correctValue Q\IOperationQuery */
        $this->assertEquals($correctValue, $operationQuery);
    }

    protected function valuesAsSelfQuery()
    {
        return $this->request([], new Q\Requests\Values(Q\Requests\Values::AS_SELF));
    }

    protected function valuesAsArrayQuery()
    {
        return $this->request([], new Q\Requests\Values(Q\Requests\Values::AS_ARRAY));
    }

    protected function valuesAsIteratorQuery()
    {
        return $this->request([], new Q\Requests\Values(Q\Requests\Values::AS_ARRAY_COMPATIBLE_ITERATOR));
    }

    protected function valuesAsTrueIteratorQuery()
    {
        return $this->request([], new Q\Requests\Values(Q\Requests\Values::AS_TRUE_ITERATOR));
    }

    protected function valuesAsCollectionQuery()
    {
        return $this->request([], new Q\Requests\Values(Q\Requests\Values::AS_COLLECTION));
    }

    protected function valuesAsTraversableQuery()
    {
        return $this->request([], new Q\Requests\Values(Q\Requests\Values::AS_TRAVERSABLE));
    }

    protected function offsetGetQuery()
    {
        return $this->request([], new Q\Requests\GetIndex($this->parameter()));
    }

    protected function offsetIssetQuery()
    {
        return $this->request([], new Q\Requests\IssetIndex($this->parameter()));
    }

    protected function isEmptyQuery()
    {
        return $this->request([], new Q\Requests\IsEmpty());
    }

    protected function countQuery()
    {
        return $this->request([], new Q\Requests\Count());
    }

    protected function firstQuery()
    {
        return $this->request([], new Q\Requests\First());
    }

    protected function lastQuery()
    {
        return $this->request([], new Q\Requests\Last());
    }

    protected function aggregateQuery()
    {
        return $this->request(
                [],
                new Q\Requests\Aggregate(
                        new Q\Functions\Aggregator(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [O\Expression::parameter('a'), O\Expression::parameter('s')],
                                [
                                        O\Expression::returnExpression(
                                                O\Expression::binaryOperation(
                                                        O\Expression::variable(O\Expression::value('a')),
                                                        O\Operators\Binary::ADDITION,
                                                        O\Expression::variable(O\Expression::value('s'))
                                                )
                                        )
                                ]))
        );
    }

    protected function allQuery()
    {
        return $this->request([], new Q\Requests\All());
    }

    protected function anyQuery()
    {
        return $this->request([], new Q\Requests\Any());
    }

    protected function maximumQuery()
    {
        return $this->request([], new Q\Requests\Maximum());
    }

    protected function minimumQuery()
    {
        return $this->request([], new Q\Requests\Minimum());
    }

    protected function sumQuery()
    {
        return $this->request([], new Q\Requests\Sum());
    }

    protected function averageQuery()
    {
        return $this->request([], new Q\Requests\Average());
    }

    protected function implodeQuery()
    {
        return $this->request([], new Q\Requests\Implode($this->parameter()));
    }

    protected function setIndexQuery()
    {
        return $this->operation([], new Q\Operations\SetIndex($this->parameter(), $this->parameter()));
    }

    protected function unsetIndexQuery()
    {
        return $this->operation([], new Q\Operations\UnsetIndex($this->parameter()));
    }

    protected function applyQuery()
    {
        return $this->operation(
                [],
                new Q\Operations\Apply(
                        new Q\Functions\ElementMutator(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [O\Expression::parameter('i', null, null, true)],
                                [
                                        O\Expression::assign(
                                                O\Expression::variable(O\Expression::value('i')),
                                                O\Operators\Assignment::MULTIPLICATION,
                                                O\Expression::value(10)
                                        )
                                ]))
        );
    }

    protected function sequenceSource()
    {
        return new Q\Common\Source\ArrayOrIterator($this->parameter());
    }

    protected function singleValueSource()
    {
        return new Q\Common\Source\SingleValue($this->parameter());
    }

    protected function addRangeQuery()
    {
        return $this->operation([], new Q\Operations\AddValues($this->sequenceSource()));
    }

    protected function addQuery()
    {
        return $this->operation([], new Q\Operations\AddValues($this->singleValueSource()));
    }

    protected function removeRangeQuery()
    {
        return $this->operation([], new Q\Operations\RemoveValues($this->sequenceSource()));
    }

    protected function removeWhereQuery()
    {
        return $this->operation(
                [],
                new Q\Operations\RemoveWhere(
                        new Q\Functions\ElementProjection(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [],
                                [O\Expression::returnExpression(O\Expression::constant('true'))]))
        );
    }

    protected function removeQuery()
    {
        return $this->operation([], new Q\Operations\RemoveValues($this->singleValueSource()));
    }

    protected function clearQuery()
    {
        return $this->operation([], new Q\Operations\Clear());
    }

    protected function joinApplyQuery()
    {
        return $this->operation([], new Q\Operations\JoinApply(
                new Q\Common\Join\Options(
                        $this->sequenceSource(),
                        false,
                        new Q\Common\Join\Filter\Custom(
                                new Q\Functions\ConnectorProjection(
                                        $this->parameter(),
                                        self::SCOPE_TYPE,
                                        self::SCOPE_NAMESPACE,
                                        [$this->parameter() => 'this'],
                                        [],
                                        [O\Expression::returnExpression(O\Expression::constant('true'))]))),
                new Q\Functions\ConnectorMutator(
                        $this->parameter(),
                        self::SCOPE_TYPE,
                        self::SCOPE_NAMESPACE,
                        [$this->parameter() => 'this'],
                        [O\Expression::parameter('o', null, null, true), O\Expression::parameter('i')],
                        [
                                O\Expression::assign(
                                        O\Expression::variable(O\Expression::value('o')),
                                        O\Operators\Assignment::MULTIPLICATION,
                                        O\Expression::variable(O\Expression::value('i'))
                                )
                        ])));
    }

    protected function whereQuery()
    {
        return $this->scopeRequest(
                [new Q\Segments\Filter(
                        new Q\Functions\ElementProjection(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [],
                                [O\Expression::returnExpression(O\Expression::constant('true'))]))]);
    }

    protected function selfProjection($variableName = 'i')
    {
        return new Q\Functions\ElementProjection(
                $this->parameter(),
                self::SCOPE_TYPE,
                self::SCOPE_NAMESPACE,
                [$this->parameter() => 'this'],
                [O\Expression::parameter($variableName)],
                [O\Expression::returnExpression(O\Expression::variable(O\Expression::value($variableName)))]);
    }

    protected function orderByAscendingQuery()
    {
        return $this->scopeRequest(
                [new Q\Segments\OrderBy([new Q\Segments\Ordering($this->selfProjection(), $this->parameter())])]
        );
    }

    protected function orderByDescendingQuery()
    {
        return $this->scopeRequest(
                [new Q\Segments\OrderBy([new Q\Segments\Ordering($this->selfProjection(), $this->parameter())])]
        );
    }

    protected function orderByAscendingWithThenByAscendingQuery()
    {
        return $this->scopeRequest(
                [new Q\Segments\OrderBy(
                        [
                                new Q\Segments\Ordering($this->selfProjection(), $this->parameter()),
                                new Q\Segments\Ordering($this->selfProjection(), $this->parameter())
                        ])]
        );
    }

    protected function orderByAscendingWithThenByDescendingQuery()
    {
        return $this->scopeRequest(
                [new Q\Segments\OrderBy(
                        [
                                new Q\Segments\Ordering($this->selfProjection(), $this->parameter()),
                                new Q\Segments\Ordering($this->selfProjection(), $this->parameter())
                        ])]
        );
    }

    protected function skipQuery()
    {
        return $this->scopeRequest([new Q\Segments\Range($this->parameter(), $this->parameter())]);
    }

    protected function takeQuery()
    {
        return $this->scopeRequest([new Q\Segments\Range($this->parameter(), $this->parameter())]);
    }

    protected function indexByQuery()
    {
        return $this->scopeRequest([new Q\Segments\IndexBy($this->selfProjection())]);
    }

    protected function keysQuery()
    {
        return $this->scopeRequest([new Q\Segments\Keys()]);
    }

    protected function reindexQuery()
    {
        return $this->scopeRequest([new Q\Segments\Reindex()]);
    }

    protected function groupByQuery()
    {
        return $this->scopeRequest([new Q\Segments\GroupBy($this->selfProjection())]);
    }

    protected function arrayConnectorProjection()
    {
        return new Q\Functions\ConnectorProjection(
                $this->parameter(),
                self::SCOPE_TYPE,
                self::SCOPE_NAMESPACE,
                [$this->parameter() => 'this'],
                [O\Expression::parameter('o'), O\Expression::parameter('i')],
                [
                        O\Expression::returnExpression(O\Expression::arrayExpression([
                                O\Expression::arrayItem(null, O\Expression::variable(O\Expression::value('o'))),
                                O\Expression::arrayItem(null, O\Expression::variable(O\Expression::value('i')))
                        ]))
                ]);
    }

    protected function unfilteredJoinQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                new Q\Common\Join\Options($this->sequenceSource(), false),
                $this->arrayConnectorProjection())]);
    }

    protected function joinOnQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                new Q\Common\Join\Options(
                        $this->sequenceSource(),
                        false,
                        new Q\Common\Join\Filter\Custom(
                                new Q\Functions\ConnectorProjection(
                                        $this->parameter(),
                                        self::SCOPE_TYPE,
                                        self::SCOPE_NAMESPACE,
                                        [$this->parameter() => 'this'],
                                        [O\Expression::parameter('o'), O\Expression::parameter('i')],
                                        [O\Expression::returnExpression(
                                                O\Expression::binaryOperation(
                                                        O\Expression::variable(O\Expression::value('o')),
                                                        O\Operators\Binary::IDENTITY,
                                                        O\Expression::variable(O\Expression::value('i'))))]))),
                $this->arrayConnectorProjection())]);
    }

    protected function joinOnEqualityQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                new Q\Common\Join\Options(
                        $this->sequenceSource(),
                        false,
                        new Q\Common\Join\Filter\Equality(
                                $this->selfProjection('o'),
                                $this->selfProjection('i'))),
                $this->arrayConnectorProjection())]);
    }

    protected function joinWithDefaultQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                new Q\Common\Join\Options(
                        $this->sequenceSource(),
                        false,
                        null,
                        true,
                        $this->parameter(),
                        $this->parameter()),
                $this->arrayConnectorProjection())]);
    }

    protected function joinTwoWithDefaultsQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                new Q\Common\Join\Options(
                        $this->sequenceSource(),
                        false,
                        null,
                        true,
                        $this->parameter(),
                        $this->parameter()),
                $this->arrayConnectorProjection())]);
    }

    protected function joinToSubScopeQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                        new Q\Common\Join\Options(
                                $this->scopeSource([
                                        new Q\Segments\OrderBy([new Q\Segments\Ordering($this->selfProjection(), $this->parameter())]),
                                        new Q\Segments\Select($this->selfProjection()),
                                        new Q\Segments\Range($this->parameter(), $this->parameter()),
                                ]),
                                false,
                                null),
                        $this->arrayConnectorProjection())]);
    }

    protected function unfilteredGroupJoinWithDefaultQuery()
    {
        return $this->scopeRequest([new Q\Segments\Join(
                new Q\Common\Join\Options(
                        $this->sequenceSource(),
                        true),
                $this->arrayConnectorProjection())]);
    }

    protected function uniqueQuery()
    {
        return $this->scopeRequest([new Q\Segments\Unique()]);
    }

    protected function selectQuery()
    {
        return $this->scopeRequest([new Q\Segments\Select($this->selfProjection())]);
    }

    protected function selectManyQuery()
    {
        return $this->scopeRequest([new Q\Segments\SelectMany($this->selfProjection())]);
    }

    protected function appendQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::APPEND,
                $this->sequenceSource())]);
    }

    protected function whereInQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::WHERE_IN,
                $this->sequenceSource())]);
    }

    protected function exceptQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::EXCEPT,
                $this->sequenceSource())]);
    }

    protected function unionQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::UNION,
                $this->sequenceSource())]);
    }

    protected function intersectQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::INTERSECT,
                $this->sequenceSource())]);
    }

    protected function differenceQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::DIFFERENCE,
                $this->sequenceSource())]);
    }

    protected function intersectWithFilteredSubScopeQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::INTERSECT,
                $this->scopeSource([new Q\Segments\Filter(
                        new Q\Functions\ElementProjection(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [],
                                [O\Expression::returnExpression(O\Expression::constant('true'))]))]))]);
    }

    protected function nestedOperationsQuery()
    {
        return $this->scopeRequest([new Q\Segments\Operation(
                Q\Segments\Operation::APPEND,
                $this->scopeSource([new Q\Segments\Operation(
                        Q\Segments\Operation::WHERE_IN,
                        $this->scopeSource([new Q\Segments\Operation(
                                Q\Segments\Operation::EXCEPT,
                                $this->scopeSource([new Q\Segments\Operation(
                                        Q\Segments\Operation::UNION,
                                        $this->scopeSource([new Q\Segments\Operation(
                                                Q\Segments\Operation::INTERSECT,
                                                $this->scopeSource([new Q\Segments\Operation(
                                                        Q\Segments\Operation::DIFFERENCE,
                                                        $this->scopeSource([new Q\Segments\Range(
                                                                $this->parameter(),
                                                                $this->parameter()
                                                        )])
                                                )])
                                        )])
                                )])
                        )])
        )]))]);
    }

    protected function indexProjection($index, $variableName = 'row')
    {
        return new Q\Functions\ElementProjection(
                $this->parameter(),
                self::SCOPE_TYPE,
                self::SCOPE_NAMESPACE,
                [$this->parameter() => 'this'],
                [O\Expression::parameter($variableName)],
                [
                        O\Expression::returnExpression(
                                O\Expression::index(
                                        O\Expression::variable(O\Expression::value($variableName)),
                                        O\Expression::value($index)
                                )
                        )
                ]);
    }

    protected function exampleFromDocsQuery()
    {
        $rowExpression = O\Expression::variable(O\Expression::value('row'));
        return $this->scopeRequest(
                [
                        new Q\Segments\Filter(new Q\Functions\ElementProjection(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [O\Expression::parameter('row')],
                                [O\Expression::returnExpression(
                                        O\Expression::binaryOperation(
                                                O\Expression::index($rowExpression, O\Expression::value('age')),
                                                O\Operators\Binary::LESS_THAN_OR_EQUAL_TO,
                                                O\Expression::value(50)))])),
                        new Q\Segments\OrderBy(
                                [
                                        new Q\Segments\Ordering($this->indexProjection('firstName'), $this->parameter()),
                                        new Q\Segments\Ordering($this->indexProjection('lastName'), $this->parameter()),
                                ]
                        ),
                        new Q\Segments\Range($this->parameter(), $this->parameter()),
                        new Q\Segments\IndexBy($this->indexProjection('phoneNumber')),
                        new Q\Segments\Select(new Q\Functions\ElementProjection(
                                $this->parameter(),
                                self::SCOPE_TYPE,
                                self::SCOPE_NAMESPACE,
                                [$this->parameter() => 'this'],
                                [O\Expression::parameter('row')],
                                [O\Expression::returnExpression(
                                        O\Expression::arrayExpression(
                                            [
                                                    O\Expression::arrayItem(
                                                            O\Expression::value('fullName'),
                                                            O\Expression::binaryOperation(
                                                                    O\Expression::binaryOperation(
                                                                            O\Expression::index($rowExpression, O\Expression::value('firstName')),
                                                                            O\Operators\Binary::CONCATENATION,
                                                                            O\Expression::value(' ')),
                                                                    O\Operators\Binary::CONCATENATION,
                                                                    O\Expression::index($rowExpression, O\Expression::value('lastName')))),
                                                    O\Expression::arrayItem(
                                                            O\Expression::value('address'),
                                                            O\Expression::index($rowExpression, O\Expression::value('address'))),
                                                    O\Expression::arrayItem(
                                                            O\Expression::value('dateOfBirth'),
                                                            O\Expression::index($rowExpression, O\Expression::value('dateOfBirth'))),
                                            ]
                                        ))]))
                ]
        );
    }

    /** @return self */
    private function getThis()
    {
        return $this;
    }

    /**
     * @dataProvider allImplementations
     */
    public function testNestedOperationsQueryWithRequiredSimplificationInScope()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->whereIn($this->getThis()->getThis()->queryable->keys());
                },
                $this->scopeRequest([new Q\Segments\Operation(
                                Q\Segments\Operation::WHERE_IN,
                                $this->scopeSource([
                                                new Q\Segments\Keys()
                                        ]))]));
    }
}
