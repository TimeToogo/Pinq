<?php

namespace Pinq;

class Queryable implements IQueryable, IOrderedQueryable
{
    /**
     * @var IQueryProvider
     */
    protected $Provider;

    /**
     * @var IQueryBuilder
     */
    protected $QueryBuilder;

    public function __construct(IQueryProvider $Provider)
    {
        $this->Provider = $Provider;
        $this->QueryBuilder = $Provider->InstantiateQueryBuilder();
    }
    
    public function AsTraversable()
    {
        return new Traversable($this->AsArray());
    }
    
    final public function AsArray()
    {
        return $this->LoadQueryScope()->Retrieve();
    }

    public function AsCollection()
    {
        return new Collection($this->AsArray());
    }

    public function __clone()
    {
        $this->QueryBuilder = clone $this->QueryBuilder;
    }

    final protected function LoadQueryScope()
    {
        if (!$this->QueryBuilder->IsEmpty()) {
            $this->Provider = $this->Provider->LoadQueryScope($this->QueryBuilder->GetStream());
            $this->QueryBuilder->ClearStream();
            $this->OnNewQueryScope();
        }

        return $this->Provider;
    }
    
    protected function OnNewQueryScope() {}

    final public function GetProvider()
    {
        return $this->Provider;
    }

    final public function GetBuilder()
    {
        return $this->QueryBuilder;
    }

    final public function getIterator()
    {
        return new \ArrayIterator($this->AsArray());
    }
    
    public function Select(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Select($Function);

        return $Clone;
    }

    public function SelectMany(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->SelectMany($Function);

        return $Clone;
    }

    public function IndexBy(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->IndexBy($Function);

        return $Clone;
    }

    public function Clear()
    {
        $this->LoadQueryScope()->Clear();
    }

    public function Where(callable $Predicate)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Where($Predicate);

        return $Clone;
    }

    public function GroupBy(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->GroupBy($Function);

        return $Clone;
    }

    public function Append(ITraversable $Traversable)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Append($Traversable);

        return $Clone;
    }

    public function Union(ITraversable $Traversable)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Union($Traversable);

        return $Clone;
    }

    public function Intersect(ITraversable $Traversable)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Intersect($Traversable);

        return $Clone;
    }

    public function Except(ITraversable $Traversable)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Except($Traversable);

        return $Clone;
    }

    public function Skip($Amount)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Skip($Amount);

        return $Clone;
    }

    public function Take($Amount)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Take($Amount);

        return $Clone;
    }

    public function Slice($Start, $Amount)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Slice($Start, $Amount);

        return $Clone;
    }

    public function OrderBy(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->OrderBy($Function);

        return $Clone;
    }

    public function OrderByDescending(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->OrderByDescending($Function);

        return $Clone;
    }

    public function ThenBy(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->ThenBy($Function);

        return $Clone;
    }

    public function ThenByDescending(callable $Function)
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->ThenByDescending($Function);

        return $Clone;
    }

    public function Unique()
    {
        $Clone = clone $this;
        $Clone->QueryBuilder->Unique();

        return $Clone;
    }

    public function Count()
    {
        return $this->LoadQueryScope()->Count();
    }

    public function Exists()
    {
        return $this->LoadQueryScope()->Exists();
    }

    public function First()
    {
        return $this->LoadQueryScope()->First();
    }

    public function Contains($Value)
    {
        return $this->LoadQueryScope()->Contains($Value);
    }

    public function Aggregate(callable $Function)
    {
        return $this->LoadQueryScope()->Aggregate($Function);
    }

    public function All(callable $Function = null)
    {
        return $this->LoadQueryScope()->All($Function);
    }

    public function Any(callable $Function = null)
    {
        return $this->LoadQueryScope()->Any($Function);
    }

    public function Average(callable $Function = null)
    {
        return $this->LoadQueryScope()->Average($Function);
    }

    public function Implode($Delimiter, callable $Function = null)
    {
        return $this->LoadQueryScope()->Implode($Delimiter, $Function);
    }

    public function Maximum(callable $Function = null)
    {
        return $this->LoadQueryScope()->Maximum($Function);
    }

    public function Minimum(callable $Function = null)
    {
        return $this->LoadQueryScope()->Minimum($Function);
    }

    public function Sum(callable $Function = null)
    {
        return $this->LoadQueryScope()->Sum($Function);
    }
}
