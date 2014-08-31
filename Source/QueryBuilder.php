<?php

namespace Pinq;

use Pinq\Expressions as O;

/**
 * Base class for a query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryBuilder
{
    /**
     * The query provider implementation
     *
     * @var Providers\IQueryProvider
     */
    protected $provider;

    /**
     * The query expression
     *
     * @var O\ValueExpression|O\TraversalExpression
     */
    protected $expression;

    public function __construct(Providers\IQueryProvider $provider, O\TraversalExpression $queryExpression = null)
    {
        $this->provider   = $provider;
        $this->expression = $queryExpression ?: O\Expression::value($this);
    }

    /**
     * @param string  $name
     * @param mixed[] $arguments
     *
     * @return O\MethodCallExpression
     */
    protected function newMethod($name, array $arguments = [])
    {
        return O\Expression::methodCall(
                $this->expression,
                O\Expression::value($name),
                array_map([O\Expression::getType(), 'argument'], array_map([O\Expression::getType(), 'value'], $arguments))
        );
    }

    /**
     * Returns a new queryable instance with the supplied query segment
     * appended to the current scope
     *
     * @param string  $name
     * @param mixed[] $arguments
     *
     * @return IQueryable
     */
    protected function newMethodSegment($name, array $arguments = [])
    {
        return $this->provider->createQueryable($this->newMethod($name, $arguments));
    }
}
