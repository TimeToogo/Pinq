<?php 

namespace Pinq\Queries;

/**
 * Base implementation for the IQuery
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Query implements IQuery
{
    /**
     * @var IScope
     */
    private $scope;
    
    public function __construct(IScope $scope)
    {
        $this->scope = $scope;
    }
    
    public final function getScope()
    {
        return $this->scope;
    }
}