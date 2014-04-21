<?php

namespace Pinq\Queries;

/**
 * Implementation of the IRequestQuery
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestQuery extends Query implements IRequestQuery
{
    /**
     * @var IRequest
     */
    private $Request;
    
    public function __construct(IScope $Scope, IRequest $Request)
    {
        parent::__construct($Scope);
        $this->Request = $Request;
    }

    public function GetRequest()
    {
        return $this->Request;
    }
}
