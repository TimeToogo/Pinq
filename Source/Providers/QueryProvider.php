<?php

namespace Pinq\Providers;

use Pinq\Queries;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Parsing\IFunctionToExpressionTreeConverter;

/**
 * Base class for the query provider, with default functionality
 * for the function to expression tree converter and request evaluation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class QueryProvider implements IQueryProvider
{
    /**
     * @var IIteratorScheme
     */
    protected $scheme;
    
    /**
     * @var IFunctionToExpressionTreeConverter
     */
    protected $functionConverter;

    public function __construct(
            \Pinq\Caching\IFunctionCache $functionCache = null, 
            IFunctionToExpressionTreeConverter $functionConverter = null,
            IIteratorScheme $scheme = null)
    {
        $this->functionConverter = $functionConverter ?: 
                new \Pinq\Parsing\FunctionToExpressionTreeConverter(
                        new \Pinq\Parsing\PHPParser\Parser(),
                        $functionCache);
        
        $this->scheme = $scheme ?: \Pinq\Iterators\SchemeProvider::getDefault();
    }

    /**
     * @return IIteratorScheme
     */
    public function getIteratorScheme()
    {
        return $this->scheme;
    }
    
    /**
     * @return IFunctionToExpressionTreeConverter
     */
    public function getFunctionToExpressionTreeConverter()
    {
        return $this->functionConverter;
    }

    public function createQueryable(Queries\IScope $scope = null)
    {
        return new \Pinq\Queryable($this, $scope, $this->scheme);
    }

    public function load(Queries\IRequestQuery $query)
    {
        return $this->loadRequestEvaluatorVisitor($query->getScope())->visit($query->getRequest());
    }

    /**
     * This should be implemented such that it returns an request visitor
     * which will load the request query
     *
     * @return Queries\Requests\RequestVisitor
     */
    abstract protected function loadRequestEvaluatorVisitor(Queries\IScope $scope);
}
