<?php

namespace Pinq\Queries\Expression;

use \Pinq\Parsing\IFunctionToExpressionTreeConverter;

class Builder extends \Pinq\Queries\Builder
{
    /**
     * @var IFunctionToExpressionTreeConverter
     */
    private $FunctionConverter;

    public function __construct(IFunctionToExpressionTreeConverter $FunctionConverter)
    {
        parent::__construct();
        $this->FunctionConverter = $FunctionConverter;
    }

    private function GetReturnExpression(callable $Function)
    {
        return $this->FunctionConverter->Convert($Function)->VerifyReturnExpression();
    }

    protected function FilterQuery(callable $Function)
    {
        return new Filter($this->GetReturnExpression($Function));
    }

    protected function OrderByQuery(array $OrderByFunctions, array $IsAscendingArray)
    {
        $OrderByExpressions = [];
        foreach ($OrderByFunctions as $Key => $OrderByFunction) {
            $OrderByExpressions[$Key] = $this->GetReturnExpression($OrderByFunction);
        }

        return new OrderBy($OrderByExpressions, $IsAscendingArray);
    }

    protected function GroupByQuery(callable $Function)
    {
        return new GroupBy($this->GetReturnExpression($Function));
    }

    protected function SelectManyQuery(callable $Function)
    {
        return new SelectMany($this->GetReturnExpression($Function));
    }

    protected function SelectQuery(callable $Function)
    {
        return new Select($this->GetReturnExpression($Function));
    }
    
    protected function IndexByQuery(callable $Function)
    {
        return new IndexBy($this->GetReturnExpression($Function));
    }
}
