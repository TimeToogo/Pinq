<?php

namespace Pinq\Queries\Functional;

class Builder extends \Pinq\Queries\Builder
{
    protected function FilterQuery(callable $Function)
    {
        return new Filter($Function);
    }

    protected function OrderByQuery(array $OrderByFunctions, array $IsAscendingArray)
    {
        return new OrderBy($OrderByFunctions, $IsAscendingArray);
    }

    protected function GroupByQuery(callable $Function)
    {
        return new GroupBy($Function);
    }

    protected function SelectManyQuery(callable $Function)
    {
        return new SelectMany($Function);
    }

    protected function SelectQuery(callable $Function)
    {
        return new Select($Function);
    }
    
    protected function IndexByQuery(callable $Function)
    {
        return new IndexBy($Function);
    }
}
