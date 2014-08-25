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

    public function __construct(IScope $scope, IRequest $request)
    {
        parent::__construct($scope, $request->getParameters());
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function update(IScope $scope, IRequest $request)
    {
        if ($this->scope === $scope && $this->request === $request) {
            return $this;
        }

        return new self($scope, $request);
    }

    public function updateRequest(IRequest $request)
    {
        return $this->update($this->scope, $request);
    }

    protected function withScope(IScope $scope)
    {
        return $this->update($scope, $this->request);
    }
}
