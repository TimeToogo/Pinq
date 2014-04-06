<?php

namespace Pinq\Queries\Operations;

use \Pinq\Queries\IOperation;

class OperationVisitor
{
    /**
     * @param mixed The returned value
     */
    final public function Visit(IOperation $Operation)
    {
        return $Operation->Traverse($this);
    }

    public function VisitApply(Apply $Operation) {}

    public function VisitAddValues(AddValues $Operation) {}

    public function VisitRemoveValues(RemoveValues $Operation) {}

    public function VisitRemoveWhere(RemoveWhere $Operation) {}

    public function VisitClear(Clear $Operation) {}
    
    public function VisitUnsetIndex(UnsetIndex $Operation) {}
    
    public function VisitSetIndex(SetIndex $Operation) {}
}
