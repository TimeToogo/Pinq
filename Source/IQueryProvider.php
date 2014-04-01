<?php

namespace Pinq;

interface IQueryProvider extends IAggregatable
{
    /**
     * Returns a new provider scoped to the resulting values of the query
     * stream from the supplied query stream.
     *
     * @return IQueryProvider
     */
    public function LoadQueryScope(Queries\IQueryStream $QueryStream = null);

    /**
     * Instantiates a new query builder associated with the provider
     *
     * @return IQueryBuilder
     */
    public function InstantiateQueryBuilder();

    /**
     * @return array
     */
    public function &Retrieve();
}
