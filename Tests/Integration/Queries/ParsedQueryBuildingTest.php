<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Expressions as O;
use Pinq\Parsing;
use Pinq\Providers;
use Pinq\Queries as Q;

abstract class ParsedQueryBuildingTest extends QueryBuildingTestBase
{
    protected function assertRequestExpressionMatches(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            $correctValue
    ) {
        $requestBuilder = $this->queryable->getProvider()->getConfiguration()->getRequestQueryBuilder();
        $requestQuery   = $requestBuilder->parseRequest($requestExpression, $evaluationContext);
        $this->assertRequestQueryMatches(
                $requestQuery,
                $requestQuery->getParameters()->resolve(
                        $requestBuilder->resolveRequest($requestExpression, $evaluationContext)
                ),
                $correctValue
        );
    }

    abstract protected function assertRequestQueryMatches(
            Q\IRequestQuery $requestQuery,
            Q\IResolvedParameterRegistry $resolvedParameters,
            $correctValue
    );

    protected function assertOperationExpressionMatches(
            O\Expression $operationExpression,
            O\IEvaluationContext $evaluationContext = null,
            $correctValue
    ) {
        $operationBuilder = $this->repository->getProvider()->getConfiguration()->getOperationQueryBuilder();
        $operationQuery   = $operationBuilder->parseOperation($operationExpression, $evaluationContext);
        $this->assertOperationQueryMatches(
                $operationQuery,
                $operationQuery->getParameters()->resolve(
                        $operationBuilder->resolveOperation($operationExpression, $evaluationContext)
                ),
                $correctValue
        );
    }

    abstract protected function assertOperationQueryMatches(
            Q\IOperationQuery $operationQuery,
            Q\IResolvedParameterRegistry $resolvedParameters,
            $correctValue
    );


    public function parameter()
    {
        return QueryComparator::parameter();
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

    protected function sequenceSource()
    {
        return new Q\Common\Source\ArrayOrIterator($this->parameter());
    }

    protected function singleValueSource()
    {
        return new Q\Common\Source\SingleValue($this->parameter());
    }
}
