<?php

namespace Pinq\Providers\Collection;

use \Pinq\Queries\Operations;

/**
 * Evaluates the operations on the supplied collection instance
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OperationEvaluator extends Operations\OperationVisitor
{
    /**
     * @var \Pinq\ICollection
     */
    private $Collection;
    
    public function __construct(\Pinq\ICollection $Collection)
    {
        $this->Collection = $Collection;
    }
    
    public function VisitApply(Operations\Apply $Operation)
    {
        $this->Collection->Apply($Operation->GetFunctionExpressionTree());
    }

    public function VisitAddValues(Operations\AddValues $Operation)
    {
        $this->Collection->AddRange($Operation->GetValues());
    }

    public function VisitRemoveValues(Operations\RemoveValues $Operation)
    {
        $this->Collection->RemoveRange($Operation->GetValues());
    }

    public function VisitRemoveWhere(Operations\RemoveWhere $Operation)
    {
        $this->Collection->RemoveWhere($Operation->GetFunctionExpressionTree());
    }
    public function VisitClear(Operations\Clear $Operation)
    {
        $this->Collection->Clear();
    }

    public function VisitSetIndex(Operations\SetIndex $Operation)
    {
        $this->Collection[$Operation->GetIndex()] = $Operation->GetValue();
    }

    public function VisitUnsetIndex(Operations\UnsetIndex $Operation)
    {
        unset($this->Collection[$Operation->GetIndex()]);
    }

}
