<?php

namespace Pinq\Providers\Memory;

use \Pinq\Queries;
use \Pinq\Providers\Base;

abstract class Provider extends Base\Provider implements \Pinq\IQueryProvider
{
    protected $Values;

    /**
     * @var IQueryStreamEvaluator
     */
    private $Evaluator;

    public function __construct(array &$Values, IQueryStreamEvaluator $Evaluator)
    {
        $this->Values =& $Values;
        $this->Evaluator = $Evaluator;
        $this->ResetQueryStreamEvaluator();
    }

    private function ResetQueryStreamEvaluator()
    {
        $this->Evaluator->SetValues($this->Values);
    }

    public function InstantiateQueryBuilder()
    {
        return new Queries\Functional\Builder();
    }

    public function LoadQueryStreamScope(Queries\IQueryStream $QueryStream)
    {
        $Values = $this->LoadQueryStream($QueryStream);
        $NewScope = $this->NewScope($Values);
        $NewScope->Evaluator = clone $this->Evaluator;
        
        return $NewScope;
    }
    abstract protected function NewScope(array $Values);

    final public function &Retrieve()
    {
        return $this->Values;
    }
    
    private function LoadQueryStream(Queries\IQueryStream $QueryStream)
    {
        $this->Evaluator->Evaluate($QueryStream);

        $Values = $this->Evaluator->GetValues();

        $this->ResetQueryStreamEvaluator();

        return $Values;
    }

    final public function Count()
    {
        return count($this->Values);
    }

    final public function Exists()
    {
        return !empty($this->Values);
    }

    final public function First()
    {
        return empty($this->Values) ? null : reset($this->Values);
    }

    final public function Aggregate(callable $Function)
    {
        return array_reduce($this->Values, $Function, null);
    }

}
