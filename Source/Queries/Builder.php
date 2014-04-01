<?php

namespace Pinq\Queries;

abstract class Builder implements \Pinq\IQueryBuilder
{
    /**
     * @var Queries\IQuery[]
     */
    private $Queries = [];

    /**
     * @var callable[]
     */
    private $OrderFunctions = [];

    /**
     * @var boolean[]
     */
    private $IsAscendingArray = [];

    public function __construct()
    {
    }

    final public function ClearStream()
    {
        $this->OrderFunctions = [];
        $this->IsAscendingArray = [];
        $this->Queries = [];
    }

    final public function IsEmpty()
    {
        return empty($this->Queries) && count($this->OrderFunctions) === 0;
    }

    final public function GetStream()
    {
        $this->FlushOrderByQueries();

        return new QueryStream($this->Queries);
    }

    private function FlushOrderByQueries()
    {
        if (count($this->OrderFunctions) > 0) {
            $this->Queries[] = $this->OrderByQuery($this->OrderFunctions, $this->IsAscendingArray);
            $this->OrderFunctions = [];
            $this->IsAscendingArray = [];
        }
    }
    abstract protected function OrderByQuery(array $OrderByFunctions, array $IsAscendingArray);

    final public function Select(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = $this->SelectQuery($Function);
    }
    abstract protected function SelectQuery(callable $Function);

    final public function SelectMany(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = $this->SelectManyQuery($Function);
    }
    abstract protected function SelectManyQuery(callable $Function);

    final public function IndexBy(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = $this->IndexByQuery($Function);
    }
    abstract protected function IndexByQuery(callable $Function);

    final public function Where(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = $this->FilterQuery($Function);
    }
    abstract protected function FilterQuery(callable $Function);

    final public function OrderBy(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->OrderFunctions[] = $Function;
        $this->IsAscendingArray[] = true;
    }

    final public function OrderByDescending(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->OrderFunctions[] = $Function;
        $this->IsAscendingArray[] = false;
    }

    final public function ThenBy(callable $Function)
    {
        $this->OrderFunctions[] = $Function;
        $this->IsAscendingArray[] = true;
    }

    final public function ThenByDescending(callable $Function)
    {
        $this->OrderFunctions[] = $Function;
        $this->IsAscendingArray[] = false;
    }

    final public function Skip($Amount)
    {
        $this->FlushOrderByQueries();

        return $this->Slice($Amount, null);
    }

    final public function Take($Amount)
    {
        $this->FlushOrderByQueries();

        return $this->Slice(0, $Amount);
    }

    final public function Slice($Start, $Amount)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = new Queries\Range($Start, $Amount);
    }

    public function GroupBy(callable $Function)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = $this->GroupByQuery($Function);
    }
    abstract protected function GroupByQuery(callable $Function);

    final public function Union(\Pinq\ITraversable $Traversable)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = new Operation(Operation::Union, $Traversable);
    }

    final public function Append(\Pinq\ITraversable $Traversable)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = new Operation(Operation::Append, $Traversable);
    }

    final public function Intersect(\Pinq\ITraversable $Traversable)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = new Operation(Operation::Intersect, $Traversable);
    }

    final public function Except(\Pinq\ITraversable $Traversable)
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = new Operation(Operation::Except, $Traversable);
    }

    final public function Unique()
    {
        $this->FlushOrderByQueries();
        $this->Queries[] = new Unique();
    }
}
