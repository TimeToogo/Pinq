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
    private $request;
    
    public function __construct(IScope $scope, IRequest $request)
    {
        parent::__construct($scope);
        $this->request = $request;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
}