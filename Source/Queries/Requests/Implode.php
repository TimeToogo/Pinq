<?php

namespace Pinq\Queries\Requests; 

use \Pinq\FunctionExpressionTree;

class Implode extends ProjectionRequest
{
    /**
     * @var string
     */
    private $Delimiter;
    
    public function __construct($Delimiter, FunctionExpressionTree $FunctionExpressionTree = null)
    {
        parent::__construct($FunctionExpressionTree);
        $this->Delimiter = $Delimiter;
    }
    
    public function GetType()
    {
        return self::Implode;
    }
    
    /**
     * @return string
     */
    public function GetDelimiter()
    {
        return $this->Delimiter;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitImplode($this);
    }
}
