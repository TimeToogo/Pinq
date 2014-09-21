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
     * @var Queries\IRequestQuery
     */
    private $requestQuery;

    public function __construct(IScopeProcessor $scopeProcessor, Queries\IRequestQuery $requestQuery)
    {
        parent::__construct($scopeProcessor);
        $this->requestQuery = $requestQuery;
    }

    public function buildQuery()
    {
        $scope   = $this->scopeProcessor->buildScope();
        $request = $this->requestQuery->getRequest();

        return $this->requestQuery->update(
                $this->processScope($scope, $request),
                $this->processRequest($scope, $request)
        );
    }

    /**
     * @param Queries\IScope   $scope
     * @param Queries\IRequest $request
     *
     * @return Queries\IScope
     */
    protected function processScope(Queries\IScope $scope, Queries\IRequest $request)
    {
        return $scope;
    }

    /**
     * @param Queries\IScope   $scope
     * @param Queries\IRequest $request
     *
     * @return Queries\IRequest
     */
    protected function processRequest(Queries\IScope $scope, Queries\IRequest $request)
    {
        return $request;
    }
}
