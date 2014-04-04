<?php

namespace Pinq\Providers\Traversable;

use \Pinq\Queries;

class Provider extends \Pinq\Providers\QueryProvider
{
    private $QueryStreamEvaluator;
    protected $Traversable;

    public function __construct(\Pinq\ITraversable $Traversable)
    {
        parent::__construct(new \Pinq\Parsing\FunctionToExpressionTreeConverter(new \Pinq\Parsing\PHPParser\Parser()));//TODO: Lazy?
        $this->QueryStreamEvaluator = new QueryStreamEvaluator();
        $this->Traversable = $Traversable;
    }

    public function Scope(Queries\IQueryStream $QueryStream)
    {
        $this->QueryStreamEvaluator->SetTraversable($this->Traversable);
        $this->QueryStreamEvaluator->Walk($QueryStream);
        
        return new QueryScope($this->QueryStreamEvaluator->GetTraversable());
    }
}
