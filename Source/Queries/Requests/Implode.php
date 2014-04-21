<?php

namespace Pinq\Queries\Requests; 

use \Pinq\FunctionExpressionTree;

/**
 * Request query for a string of all the projected values 
 * concatenated by the specified delimiter
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Implode extends ProjectionRequest
{
    /**
     * @var string
     */
    private $Delimiter;
    
    /**
     * @param string $Delimiter
     */
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
