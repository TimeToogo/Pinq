<?php

namespace Pinq;

class Queryable implements IQueryable
{
    /**
     * @var Providers\IQueryProvider
     */
    protected $Provider;
    
    /**
     * @var Parsing\IFunctionToExpressionTreeConverter
     */
    protected $FunctiontConverter;
    
    /**
     * @var Providers\IQueryScope
     */
    protected $Scope;
    
    /**
     * @var Queries\IQueryStream
     */
    protected $QueryStream;
    
    private $ValuesIterator = null;
    private $Values = null;

    public function __construct(Providers\IQueryProvider $Provider, Providers\IQueryScope $Scope = null)
    {
        $this->Provider = $Provider;
        $this->FunctiontConverter = $Provider->GetFunctionToExpressionTreeConverter();
        $this->Scope = $Scope ?: $Provider->Scope(new Queries\QueryStream([]));
        $this->QueryStream = $this->Scope->GetQueryStream();
    }
    
    final protected function NewQuery(Queries\IQuery $Query)
    {
        return $this->Provider->CreateQueryable($this->QueryStream->Append($Query));
    }
    
    final protected function UpdateLastQuery(Queries\IQuery $Query)
    {
        return $this->Provider->CreateQueryable($this->QueryStream->UpdateLast($Query));
    }
    
    private function Load() {
        if($this->ValuesIterator === null) {
            $this->ValuesIterator = $this->Scope->GetValues();
        }
    }
    
    final public function AsArray()
    {
        $this->Load();
        $this->Values = Utilities::ToArray($this->ValuesIterator);
        
        return $this->Values;
    }
    
    public function AsTraversable()
    {
        return new Traversable($this->AsArray());
    }

    public function AsCollection()
    {
        return new Collection($this->AsArray());
    }
    
    public function AsQueryable()
    {
        return $this;
    }

    final public function getIterator()
    {
        $this->Load();
        
        return $this->Values ? new \ArrayIterator($this->Values) : $this->ValuesIterator;
    }

    final public function GetProvider()
    {
        return $this->Provider;
    }
    
    public function GetQueryStream()
    {
        return $this->QueryStream;
    }
    
    public function GetQueryScope()
    {
        return $this->Scope;
    }
    
    final protected function Convert(callable $Function = null) 
    {
        return $Function === null ? null : $this->FunctiontConverter->Convert($Function);
    }
    
    public function Select(callable $Function)
    {
        return $this->NewQuery(new Queries\Select($this->Convert($Function)));
    }

    public function SelectMany(callable $Function)
    {
        return $this->NewQuery(new Queries\SelectMany($this->Convert($Function)));
    }

    public function IndexBy(callable $Function)
    {
        return $this->NewQuery(new Queries\IndexBy($this->Convert($Function)));
    }

    public function Where(callable $Predicate)
    {
        return $this->NewQuery(new Queries\Filter($this->Convert($Predicate)));
    }

    public function GroupBy(callable $Function)
    {
        return $this->NewQuery(new Queries\GroupBy([$this->Convert($Function)]));
    }

    public function Append(ITraversable $Values)
    {
        return $this->NewQuery(new Queries\Operation(Queries\Operation::Append, $Values));
    }

    public function Union(ITraversable $Values)
    {
        return $this->NewQuery(new Queries\Operation(Queries\Operation::Union, $Values));
    }

    public function Intersect(ITraversable $Values)
    {
        return $this->NewQuery(new Queries\Operation(Queries\Operation::Intersect, $Values));
    }

    public function Except(ITraversable $Values)
    {
        return $this->NewQuery(new Queries\Operation(Queries\Operation::Except, $Values));
    }

    public function Skip($Amount)
    {
        return $this->NewQuery(new Queries\Range($Amount, null));
    }

    public function Take($Amount)
    {
        return $this->NewQuery(new Queries\Range(0, $Amount));
    }

    public function Slice($Start, $Amount)
    {
        return $this->NewQuery(new Queries\Range($Start, $Amount));
    }

    public function OrderBy(callable $Function)
    {
        return $this->NewQuery(new Queries\OrderBy([$Function], [true]));
    }

    public function OrderByDescending(callable $Function)
    {
        return $this->NewQuery(new Queries\OrderBy([$Function], [false]));
    }
    
    public function Unique()
    {
        return $this->NewQuery(new Queries\Unique());
    }

    public function Count()
    {
        return $this->Scope->Count();
    }

    public function Exists()
    {
        return $this->Scope->Exists();
    }

    public function First()
    {
        return $this->Scope->First();
    }

    public function Contains($Value)
    {
        return $this->Scope->Contains($Value);
    }

    public function Aggregate(callable $Function)
    {
        return $this->Scope->Aggregate($this->Convert($Function));
    }

    public function All(callable $Function = null)
    {
        return $this->Scope->All($this->Convert($Function));
    }

    public function Any(callable $Function = null)
    {
        return $this->Scope->Any($this->Convert($Function));
    }

    public function Maximum(callable $Function = null)
    {
        return $this->Scope->Maximum($this->Convert($Function));
    }

    public function Minimum(callable $Function = null)
    {
        return $this->Scope->Minimum($this->Convert($Function));
    }

    public function Sum(callable $Function = null)
    {
        return $this->Scope->Sum($this->Convert($Function));
    }

    public function Average(callable $Function = null)
    {
        return $this->Scope->Average($this->Convert($Function));
    }

    public function Implode($Delimiter, callable $Function = null)
    {
        return $this->Scope->Implode($Delimiter, $this->Convert($Function));
    }
}
