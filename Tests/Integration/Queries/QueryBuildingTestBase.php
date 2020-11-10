<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Direction;
use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\IRepository;
use Pinq\Providers;
use Pinq\Parsing;
use Pinq\Queries as Q;

abstract class QueryBuildingTestBase extends \Pinq\Tests\PinqTestCase
{
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

    protected function setUp(): void
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
        if (!$onlyAsParsedExpression) {
            $this->assertRequestExpressionMatches($requestFunction($this->queryable)->getExpression(), null, $correctValue);
        }

        $requestExpression = $this->parseQueryExpression($requestFunction, $evaluationContext);
        $this->assertRequestExpressionMatches($requestExpression, $evaluationContext, $correctValue);
    }

    abstract protected function assertRequestExpressionMatches(O\Expression $requestExpression, O\IEvaluationContext $evaluationContext = null, $correctValue);

    final protected function assertOperationIsCorrect(callable $operationFunction, $correctValue)
    {
        $operationExpression = $this->parseQueryExpression($operationFunction, $evaluationContext);
        $this->assertOperationExpressionMatches($operationExpression, $evaluationContext, $correctValue);
    }

    abstract protected function assertOperationExpressionMatches(O\Expression $operationExpression, O\IEvaluationContext $evaluationContext = null, $correctValue);

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
        foreach ([$parameterName => $this->queryable, 'this' => $this] as $variable => $value) {
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

        if ($expression instanceof O\ReturnExpression) {
            return $expression->getValue();
        } else {
            return $expression;
        }
    }

    protected function assertEqualsButIgnoreParameterIds($expected, $actual)
    {
        QueryComparator::assertEqualsButIgnoreParameterIds($expected, $actual);
    }
}
