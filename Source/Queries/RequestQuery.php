<?php

namespace Pinq\Queries;

/**
 * Implementation of the IRequestQuery
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestQuery extends Query implements IRequestQuery
{
    /**
     * @var IRequest
     */
    private $request;

    public function __construct(IScope $scope, IRequest $request, IParameterRegistry $parameters)
    {
        parent::__construct($scope, $parameters);
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
