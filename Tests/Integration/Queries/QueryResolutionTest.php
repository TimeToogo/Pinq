<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Collection;
use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\IRepository;
use Pinq\Providers;
use Pinq\Queries as Q;
use Pinq\Queries\IResolvedQuery;
use Pinq\Traversable;

class QueryResolutionTest extends QueryBuildingTestBase
{
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

    protected function assertRequestExpressionMatches(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            $correctValue
    ) {
        /** @var $correctValue IResolvedQuery */
        $requestBuilder = $this->queryable->getProvider()->getConfiguration()->getRequestQueryBuilder();
        $resolvedQuery   = $requestBuilder->resolveRequest($requestExpression, $evaluationContext);

        $this->assertSame($correctValue->getQueryable(), $resolvedQuery->getQueryable());
        $this->assertEquals(array_values($correctValue->getResolvedParameters()), array_values($resolvedQuery->getResolvedParameters()));
    }

    protected function assertOperationExpressionMatches(
            O\Expression $operationExpression,
            O\IEvaluationContext $evaluationContext = null,
            $correctValue
    ) {
        /** @var $correctValue IResolvedQuery */
        $operationBuilder = $this->repository->getProvider()->getConfiguration()->getOperationQueryBuilder();
        $resolvedQuery   = $operationBuilder->resolveOperation($operationExpression, $evaluationContext);

        $this->assertSame($correctValue->getQueryable(), $resolvedQuery->getQueryable());
        $this->assertEquals(array_values($correctValue->getResolvedParameters()), array_values($resolvedQuery->getResolvedParameters()));
    }

    protected function resolvedRequest(array $parameters)
    {
        return new Q\ResolvedQuery($this->queryable, $parameters, null);
    }

    protected function resolvedOperation(array $parameters)
    {
        return new Q\ResolvedQuery($this->repository, $parameters, null);
    }

    /**
     * @dataProvider allImplementations
     */
    public function testEmptyQuery()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable;
                },
                $this->resolvedRequest([]));
    }

    /**
     * @dataProvider allImplementations
     */
    public function testRange()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->slice(5, 10);
                },
                $this->resolvedRequest([5, 10]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->skip(5);
                },
                $this->resolvedRequest([5, null]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->take(10);
                },
                $this->resolvedRequest([0, 10]));
    }

    /**
     * @dataProvider allImplementations
     */
    public function testWhereWithFunction()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->where('is_string');
                },
                $this->resolvedRequest(['is_string', /* $this object: */  null]));
    }

    /**
     * @dataProvider allImplementations
     */
    public function testWhereWithClosure()
    {
        $scopedVariable = 5;
        $predicate = function ($i) use ($scopedVariable) { return $i > $scopedVariable; };

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) use ($predicate) {
                    return $queryable->where($predicate);
                },
                $this->resolvedRequest([$predicate, /* $this object: */  $this, /* $scopedVariable: */  5]));
    }

    /**
     * @dataProvider allImplementations
     */
    public function testSetOperations()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->append([1, 2, 3]);
                },
                $this->resolvedRequest([[1, 2, 3]]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->union([1, 2, 3]);
                },
                $this->resolvedRequest([[1, 2, 3]]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->intersect([1, 2, 3]);
                },
                $this->resolvedRequest([[1, 2, 3]]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->whereIn([1, 2, 3]);
                },
                $this->resolvedRequest([[1, 2, 3]]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->difference([1, 2, 3]);
                },
                $this->resolvedRequest([[1, 2, 3]]));

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->except([1, 2, 3]);
                },
                $this->resolvedRequest([[1, 2, 3]]));
    }

    /**
     * @dataProvider allImplementations
     */
    public function testJoinWithDefault()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->join(['111'])->withDefault('<VALUE>', '<KEY>')->to('strlen');
                },
                $this->resolvedRequest([['111'], '<VALUE>', '<KEY>', 'strlen', /* $this object: */  null]));
    }

    /**
     * @dataProvider allImplementations
     */
    public function testJoinWithMultipleDefaults()
    {

        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable->join(['11', 2])
                            ->withDefault('<VALUE>', '<KEY>')
                            ->withDefault('<VALUE2>', '<KEY2>')
                            ->withDefault('<LAST VALUE>', '<LAST KEY>')
                            ->to('strlen');
                },
                $this->resolvedRequest([['11', 2], '<LAST VALUE>', '<LAST KEY>', 'strlen', /* $this object: */ null]));
    }

    /**
     * @dataProvider repositories
     */
    public function testApply()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->apply('sort');
                },
                $this->resolvedOperation(['sort', /* $this object: */  null]));
    }
}
