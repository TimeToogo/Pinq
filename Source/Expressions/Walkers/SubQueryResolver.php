<?php 

namespace Pinq\Expressions\Walkers;

use Pinq\Queries;
use Pinq\Expressions as O;

/**
 * Mock implementation, all supplied functions should already be expression trees
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionToExpressionTreeConverter implements \Pinq\Parsing\IFunctionToExpressionTreeConverter
{
    public function convert(callable $function)
    {
        return $function;
    }
}

/**
 * Mock implementation, used to get the request query from the queryable method calls,
 * it stores the request and can be retrieved for resolving the sub query expression
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class QueryProvider implements \Pinq\Providers\IQueryProvider
{
    private $query;
    
    public function createQueryable(\Pinq\Queries\IScope $scope = null)
    {
        return new \Pinq\Queryable($this, $scope);
    }
    
    public function getFunctionToExpressionTreeConverter()
    {
        return new FunctionToExpressionTreeConverter();
    }
    
    public function load(Queries\IRequestQuery $query)
    {
        $this->query = $query;
        
        if ($query->getRequest() instanceof Queries\Requests\Values) {
            return new \ArrayIterator();
        }
        else {
            return null;
        }
    }
    
    /**
     * @return Queries\IRequestQuery|null
     */
    public function getAndResetQuery()
    {
        $query = $this->query;
        $this->query = null;
        
        return $query;
    }
}

/**
 * Resolves the appropriate method call expression to their equivalent sub query expressions. 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SubQueryResolver extends O\ExpressionWalker
{
    /**
     * The mock query provider to store the query
     * 
     * @var QueryProvider
     */
    private $queryProvider;
    
    /**
     * The filter function determine the appropriate origin expressions
     * 
     * @var callable|null
     */
    private $filter;
    
    public function __construct()
    {
        $this->queryProvider = new QueryProvider();
    }
    
    public function setFilter(callable $function = null)
    {
        $this->filter = $function;
    }
    
    public function walkMethodCall(O\MethodCallExpression $methodCallExpression)
    {
        $expression = $methodCallExpression;
        $methodCallDepth = 0;
        $filter = $this->filter;
        
        while ($expression instanceof O\MethodCallExpression) {
            if ($filter === null || $filter($expression)) {
                $subQueryExpression = 
                        $this->resolveSubQuery(
                                $methodCallExpression,
                                $expression->getValueExpression(),
                                $methodCallDepth);
                
                if ($subQueryExpression !== null) {
                    return $subQueryExpression;
                }
            }
            
            $methodCallDepth++;
            $expression = $expression->getValueExpression();
        }
        
        return parent::walkMethodCall($methodCallExpression);
    }
    
    /**
     * @param O\MethodCallExpression $methodCallExpression
     * @param int $methodCallDepth
     * @return O\SubQueryExpression|null
     */
    private function resolveSubQuery(O\MethodCallExpression $methodCallExpression, O\Expression $queryableOriginExpression, $methodCallDepth)
    {
        $queryable = $this->queryProvider->createQueryable(new \Pinq\Queries\Scope([]));
        /*
         * Update the expression with the blank queryable as the value and resolve closure arguments
         * into fucntion expression trees
         */
        $queryMethodCallExpression = 
                $this->resolveMethodCallExpression(
                        $methodCallExpression,
                        $methodCallDepth,
                        O\Expression::value($queryable));
        // Attempt execute the methods agains the queryable
        $resultExpression = $queryMethodCallExpression->simplify();
        
        if ($resultExpression instanceof O\ValueExpression) {
            //Methods successfully executed upon the queryable, the value should contain the correct scope
            $query = $this->queryProvider->getAndResetQuery();
            
            if ($query === null) {
                $query = 
                        new Queries\RequestQuery(
                                $queryable->getScope(),
                                new Queries\Requests\Values());
            }
            
            return O\Expression::subQuery(
                    $queryableOriginExpression,
                    $query,
                    $methodCallExpression);
        }
    }
    
    private function resolveMethodCallExpression(O\MethodCallExpression $expression, $methodCallDepth, O\Expression $replacementExpression)
    {
        $expression = 
                $expression->update(
                        $expression->getValueExpression(),
                        $expression->getNameExpression(),
                        $this->resolveClosureArguments($expression->getArgumentExpressions()));
        
        if ($methodCallDepth === 0) {
            return $expression->updateValue($replacementExpression);
        }
        
        return $expression->updateValue($this->resolveMethodCallExpression(
                $expression->getValueExpression(),
                --$methodCallDepth,
                $replacementExpression));
    }
    
    private function resolveClosureArguments(array $argumentExpressions)
    {
        foreach ($argumentExpressions as $key => $argumentExpression) {
            if ($argumentExpression instanceof O\ClosureExpression) {
                $functionExpressionTree = \Pinq\FunctionExpressionTree::fromClosureExpression($argumentExpression);
                $argumentExpressions[$key] = O\Expression::value($functionExpressionTree);
            }
        }
        
        return $argumentExpressions;
    }
}