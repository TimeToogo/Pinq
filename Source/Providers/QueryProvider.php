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
    
    public function __construct(IFunctionToExpressionTreeConverter $FunctionConverter)
    {
        $this->FunctionConverter = $FunctionConverter;
    }
    
    public function GetFunctionToExpressionTreeConverter()
    {
        return $this->FunctionConverter;
    }

    public function CreateQueryable(Queries\IQueryStream $QueryStream)
    {
        if($QueryStream->IsEmpty()) {
            return new \Pinq\Queryable($this);
        }
        $Queries = $QueryStream->GetQueries();
        $LastQuery = end($Queries);
        
        if($LastQuery instanceof Queries\OrderBy) {
            return new \Pinq\OrderedQueryable($this, $this->Scope($QueryStream));
        }
        else if($LastQuery instanceof Queries\GroupBy) {
            return new \Pinq\GroupedQueryable($this, $this->Scope($QueryStream));
        }
        else {
            return new \Pinq\Queryable($this, $this->Scope($QueryStream));
        }
    }
}
