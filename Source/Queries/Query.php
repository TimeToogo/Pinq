<?php

namespace Pinq\Queries;

abstract class Query implements \Pinq\Queries\IQuery
{
    final public function Traverse(\Pinq\Queries\QueryStreamVisitor $Visitor)
    {
        if ($Visitor instanceof QueryStreamVisitor) {
            $this->TraverseQuery($Visitor);
        }
    }
    abstract protected function TraverseQuery(QueryStreamVisitor $Visitor);
}
