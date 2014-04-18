<?php

namespace Pinq\Providers;

use \Pinq\Queries;
use \Pinq\Parsing\IFunctionToExpressionTreeConverter;

abstract class QueryProvider implements IQueryProvider
{
    
    /**
     * @var IFunctionToExpressionTreeConverter 
     */
    private $FunctionConverter;
    
    public function __construct(\Pinq\Caching\IFunctionCache $FunctionCache = null)
    {
        $this->FunctionConverter = new \Pinq\Parsing\FunctionToExpressionTreeConverter(new \Pinq\Parsing\PHPParser\Parser(), $FunctionCache);
    }
    
    public function GetFunctionToExpressionTreeConverter()
    {
        return $this->FunctionConverter;
    }

    public function CreateQueryable(Queries\IScope $Scope = null)
    {
        return new \Pinq\Queryable($this, $Scope);
    }
    
    public function Load(Queries\IRequestQuery $Query)
    {
        return $this->LoadRequestEvaluatorVisitor($Query->GetScope())->Visit($Query->GetRequest());
    }
    /**
     * @return Queries\Requests\RequestVisitor
     */
    protected abstract function LoadRequestEvaluatorVisitor(Queries\IScope $Scope);
}
