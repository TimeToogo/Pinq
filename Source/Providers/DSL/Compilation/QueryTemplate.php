<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the query template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryTemplate implements IQueryTemplate
{
    /**
     * @var Queries\IQuery
     */
    protected $query;

    /**
     * @var Queries\IParameterRegistry
     */
    protected $parameters;

    /**
     * @var Parameters\StructuralExpressionRegistry
     */
    protected $structuralExpressions;

    public function __construct(
            Queries\IQuery $query = null,
            Queries\IParameterRegistry $expressions,
            Parameters\StructuralExpressionRegistry $structuralExpressions
    ) {
        $this->query                 = $query;
        $this->parameters            = $expressions;
        $this->structuralExpressions = $structuralExpressions;
    }

    final public function getQuery()
    {
        return $this->query;
    }

    final public function getParameters()
    {
        return $this->parameters;
    }

    final public function getStructuralExpressions()
    {
        return $this->structuralExpressions;
    }

    final public function getStructuralExpressionProcessors()
    {
        return $this->structuralExpressions->getProcessors();
    }

    public function resolveStructuralExpressions(Queries\IResolvedParameterRegistry $parameterRegistry, &$hash)
    {
        $hash                          = '';
        $resolvedStructuralExpressions = $this->structuralExpressions->resolve($parameterRegistry);
        foreach ($resolvedStructuralExpressions->getProcessors() as $processor) {
            $hash .= '::';
            $structuralExpressions = $resolvedStructuralExpressions->getExpressions($processor);
            foreach ($structuralExpressions->getExpressions() as $expression) {
                $hash .= '_' . $processor->hash($expression, $structuralExpressions);
            }
        }

        return $resolvedStructuralExpressions;
    }
}
