<?php

namespace Pinq;

use \Pinq\Queries;
use \Pinq\Queries\Requests;
use \Pinq\Queries\Segments;

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
     * @var Providers\IScopedRequestEvaluator
     */
    protected $Scope;
    
    /**
     * @var Queries\IScope
     */
    protected $QueryStream;
    
    private $ValuesIterator = null;
    private $Values = null;

    public function __construct(Providers\IQueryProvider $Provider, Queries\IScope $Scope = null)
    {
        $this->Provider = $Provider;
        $this->FunctiontConverter = $Provider->GetFunctionToExpressionTreeConverter();
        $this->Scope = $Scope ?: new Queries\Scope([]);
    }
    
    final protected function NewSegment(Queries\ISegment $Segment)
    {
        return $this->Provider->CreateQueryable($this->Scope->Append($Segment));
    }
    
    final protected function UpdateLastSegment(Queries\ISegment $Segment)
    {
        return $this->Provider->CreateQueryable($this->Scope->UpdateLast($Segment));
    }
    
    private function LoadQuery(Queries\IRequest $Request) 
    {
        return $this->Provider->Load(new Queries\RequestQuery($this->Scope, $Request));
    }
    
    private function Load() {
        if($this->ValuesIterator === null) {
            $this->ValuesIterator = $this->LoadQuery(new Requests\Values());
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
        $this->Load();
        return new Traversable($this->ValuesIterator);
    }

    public function AsCollection()
    {
        $this->Load();
        return new Collection($this->ValuesIterator);
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
    
    public function GetScope()
    {
        return $this->Scope;
    }
    
    final protected function Convert(callable $Function = null) 
    {
        return $Function === null ? null : $this->FunctiontConverter->Convert($Function);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Query segments">
    
    public function Select(callable $Function)
    {
        return $this->NewSegment(new Segments\Select($this->Convert($Function)));
    }

    public function SelectMany(callable $Function)
    {
        return $this->NewSegment(new Segments\SelectMany($this->Convert($Function)));
    }

    public function IndexBy(callable $Function)
    {
        return $this->NewSegment(new Segments\IndexBy($this->Convert($Function)));
    }

    public function Where(callable $Predicate)
    {
        return $this->NewSegment(new Segments\Filter($this->Convert($Predicate)));
    }

    public function GroupBy(callable $Function)
    {
        return $this->NewSegment(new Segments\GroupBy([$this->Convert($Function)]));
    }

    public function Append(ITraversable $Values)
    {
        return $this->NewSegment(new Segments\Operation(Segments\Operation::Append, $Values));
    }

    public function Union(ITraversable $Values)
    {
        return $this->NewSegment(new Segments\Operation(Segments\Operation::Union, $Values));
    }

    public function Intersect(ITraversable $Values)
    {
        return $this->NewSegment(new Segments\Operation(Segments\Operation::Intersect, $Values));
    }

    public function Except(ITraversable $Values)
    {
        return $this->NewSegment(new Segments\Operation(Segments\Operation::Except, $Values));
    }

    public function Skip($Amount)
    {
        return $this->NewSegment(new Segments\Range($Amount, null));
    }

    public function Take($Amount)
    {
        return $this->NewSegment(new Segments\Range(0, $Amount));
    }

    public function Slice($Start, $Amount)
    {
        return $this->NewSegment(new Segments\Range($Start, $Amount));
    }

    public function OrderByAscending(callable $Function)
    {
        return $this->NewSegment(new Segments\OrderBy([$Function], [true]));
    }

    public function OrderByDescending(callable $Function)
    {
        return $this->NewSegment(new Segments\OrderBy([$Function], [false]));
    }
    
    public function OrderBy(callable $Function, $Direction)
    {
        return $this->NewSegment(new Segments\OrderBy([$Function], [$Direction !== Direction::Descending]));
    }
    
    public function Unique()
    {
        return $this->NewSegment(new Segments\Unique());
    }
    
    // </editor-fold>
    // 
    // <editor-fold defaultstate="collapsed" desc="Query Requests">

    public function First()
    {
        return $this->LoadQuery(new Requests\First());
    }


    public function Last()
    {
        return $this->LoadQuery(new Requests\Last());
    }

    public function Count()
    {
        return $this->LoadQuery(new Requests\Count());
    }

    public function Exists()
    {
        return $this->LoadQuery(new Requests\Exists());
    }

    public function Contains($Value)
    {
        return $this->LoadQuery(new Requests\Contains($Value));
    }

    public function Aggregate(callable $Function)
    {
        return $this->LoadQuery(new Requests\Aggregate($this->Convert($Function)));
    }

    public function All(callable $Function = null)
    {
        return $this->LoadQuery(new Requests\All($this->Convert($Function)));
    }

    public function Any(callable $Function = null)
    {
        return $this->LoadQuery(new Requests\Any($this->Convert($Function)));
    }

    public function Maximum(callable $Function = null)
    {
        return $this->LoadQuery(new Requests\Maximum($this->Convert($Function)));
    }

    public function Minimum(callable $Function = null)
    {
        return $this->LoadQuery(new Requests\Minimum($this->Convert($Function)));
    }

    public function Sum(callable $Function = null)
    {
        return $this->LoadQuery(new Requests\Sum($this->Convert($Function)));
    }

    public function Average(callable $Function = null)
    {
        return $this->LoadQuery(new Requests\Average($this->Convert($Function)));
    }

    public function Implode($Delimiter, callable $Function = null)
    {
        return $this->LoadQuery(new Requests\Implode($Delimiter, $this->Convert($Function)));
    }

    // </editor-fold>

}
