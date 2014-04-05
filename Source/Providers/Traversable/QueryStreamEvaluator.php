<?php

namespace Pinq\Providers\Traversable;

use \Pinq\Queries;

class QueryStreamEvaluator extends Queries\QueryStreamVisitor
{
    /**
     * @var \Pinq\ITraversable
     */
    protected $Traversable;

    final public function SetTraversable(\Pinq\ITraversable $Traversable)
    {
        $this->Traversable = $Traversable;
    }

    /**
     * @return \Pinq\ITraversable
     */
    public function GetTraversable()
    {
        return $this->Traversable;
    }
    
    public function VisitFilter(Queries\Filter $Query)
    {
        $this->Traversable = $this->Traversable->Where($Query->GetFunctionExpressionTree());
    }

    public function VisitRange(Queries\Range $Query)
    {
        $this->Traversable = $this->Traversable->Slice($Query->GetRangeStart(), $Query->GetRangeAmount());
    }

    public function VisitSelect(Queries\Select $Query)
    {
        $this->Traversable = $this->Traversable->Select($Query->GetFunctionExpressionTree());
    }

    public function VisitSelectMany(Queries\SelectMany $Query)
    {
        $this->Traversable = $this->Traversable->SelectMany($Query->GetFunctionExpressionTree());
    }

    public function VisitUnique(Queries\Unique $Query)
    {
        $this->Traversable = $this->Traversable->Unique();
    }

    public function VisitOrderBy(Queries\OrderBy $Query)
    {
        $IsAscendingArray = $Query->GetIsAscendingArray();
        $First = true;
        foreach($Query->GetFunctionExpressionTrees() as $Key => $FunctionExpressionTree) {
            if($IsAscendingArray[$Key]) {
                $this->Traversable = 
                        $First ? 
                        $this->Traversable->OrderBy($FunctionExpressionTree) : 
                        $this->Traversable->ThenBy($FunctionExpressionTree);
            }
            else {
                $this->Traversable = 
                        $First ? 
                        $this->Traversable->OrderByDescending($FunctionExpressionTree) : 
                        $this->Traversable->ThenByDescending($FunctionExpressionTree);
            }
            if($First) {
                $First = false;
            }
        }
    }

    public function VisitGroupBy(Queries\GroupBy $Query)
    {
        $First = true;
        foreach($Query->GetFunctionExpressionTrees() as $FunctionExpressionTree) {
            
            $this->Traversable = 
                    $First ?
                    $this->Traversable->GroupBy($FunctionExpressionTree) :
                    $this->Traversable->AndBy($FunctionExpressionTree);
            if($First) {
                $First = false;
            }
        }
    }

    protected function VisitIndexBy(Queries\IndexBy $Query)
    {
        $this->Traversable = $this->Traversable->IndexBy($Query->GetFunctionExpressionTree());
    }

    public function VisitOperation(Queries\Operation $Query)
    {
        $OtherTraversable = $Query->GetTraversable();
        switch ($Query->GetOperationType()) {
            case Queries\Operation::Union:
                $this->Traversable = $this->Traversable->Union($OtherTraversable);
                break;
            case Queries\Operation::Append:
                $this->Traversable = $this->Traversable->Append($OtherTraversable);
                break;
            case Queries\Operation::Intersect:
                $this->Traversable = $this->Traversable->Intersect($OtherTraversable);
                break;
            case Queries\Operation::Except:
                $this->Traversable = $this->Traversable->Except($OtherTraversable);
                break;
        }
    }

}
