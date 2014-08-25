<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Base class of the request query processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RequestQueryProcessor extends QueryProcessor implements IRequestQueryProcessor
{
    /**
     * @var Queries\IRequest
     */
    private $request;

    public function __construct(IScopeProcessor $scopeProcessor, Queries\IRequest $request)
    {
        parent::__construct($scopeProcessor);
        $this->request = $request;
    }

    public function buildQuery()
    {
        $scope = $this->scopeProcessor->buildScope();
        return new Queries\RequestQuery($this->processScope($scope, $this->request), $this->processRequest($scope, $this->request));
    }

    /**
     * @param Queries\IScope $scope
     * @param Queries\IRequest     $request
     *
     * @return Queries\IScope
     */
    protected function processScope(Queries\IScope $scope, Queries\IRequest $request)
    {
        return $scope;
    }

    /**
     * @param Queries\IScope $scope
     * @param Queries\IRequest     $request
     *
     * @return Queries\IRequest
     */
    protected function processRequest(Queries\IScope $scope, Queries\IRequest $request)
    {
        return $request;
    }
}