<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

/**
 * Base class of the query processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryProcessor implements IQueryProcessor
{
    /**
     * @var IScopeProcessor
     */
    protected $scopeProcessor;

    public function __construct(IScopeProcessor $scopeProcessor)
    {
        $this->scopeProcessor = $scopeProcessor;
    }

    public function getScopeProcessor()
    {
        return $this->scopeProcessor;
    }
}
