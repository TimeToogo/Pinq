<?php

namespace Pinq\Providers;

use Pinq\Caching;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Queries;

/**
 * Base class for a query / repository provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ProviderBase implements IQueryProvider
{
    /**
     * @var Configuration\IQueryConfiguration
     */
    protected $configuration;

    /**
     * @var Caching\IQueryCache
     */
    protected $queryCache;

    /**
     * @var Queries\ISourceInfo
     */
    protected $sourceInfo;

    /**
     * @var IIteratorScheme
     */
    protected $scheme;

    /**
     * @var Utilities\IQueryResultCollection|null
     */
    protected $queryResultCollection;

    public function __construct(Queries\ISourceInfo $sourceInfo, Configuration\IQueryConfiguration $configuration)
    {
        $this->sourceInfo            = $sourceInfo;
        $this->configuration         = $configuration;
        $this->queryCache            = $configuration->getQueryCache();
        $this->scheme                = $configuration->getIteratorScheme();
    }

    final public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    final public static function getType()
    {
        return get_called_class();
    }
}
