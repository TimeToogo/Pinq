<?php

namespace Pinq\Providers\Utilities;

use Pinq\Expressions as O;
use Pinq\PinqException;
use Pinq\Traversable;

/**
 * Implementation of the query results collection that will
 * cache the query results and is able to evaluate queries in memory
 * when applicable parent values are loaded:
 *
 * <code>
 * $someRows = $queryable->where(function ($row) { return $row['id'] <= 50; });
 *
 * foreach ($someRows as $row) {
 *     //This will load the values
 * }
 *
 * //This should be evaluated in memory
 * $filteredRows = $someRows->where(function ($row) { return $row['isActive'] === true; });
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class QueryResultCollection implements IQueryResultCollection
{
    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    /**
     * @var callable
     */
    protected $traversableFactory;

    public function __construct(callable $traversableFactory = null)
    {
        $this->storage            = new \SplObjectStorage();
        $this->traversableFactory = $traversableFactory ?: Traversable::factory();
    }

    public function optimizeQuery(O\Expression $queryExpression)
    {
        //Converts all values requests (->asArray(), ->asIterator(), ->asCollection(), ->asTraversable())
        //to ->asTrueIterator() such that the true underlying values are retrieved
        //and hence can be cached as a Traversable instance such that any queries
        //on scoped values can be evaluated in memory using the traversable implementation.
        if ($queryExpression instanceof O\MethodCallExpression) {
            $nameExpression = $queryExpression->getName();
            if ($nameExpression instanceof O\ValueExpression) {
                switch (strtolower($nameExpression->getValue())) {
                    case 'asarray':
                    case 'asiterator':
                    case 'ascollection':
                    case 'astraversable':
                        return $queryExpression->update(
                                $queryExpression->getValue(),
                                O\Expression::value('getTrueIterator'),
                                $queryExpression->getArguments());
                }
            }
        }

        return $queryExpression;
    }

    protected function removeGetTrueIteratorCall(O\Expression $queryExpression)
    {
        //Removes the ->getTrueIterator() method call expression so when
        //searching for applicable results the expression will be a common ancestor
        if ($queryExpression instanceof O\MethodCallExpression) {
            $nameExpression = $queryExpression->getName();
            if ($nameExpression instanceof O\ValueExpression) {
                if (strtolower($nameExpression->getValue()) === 'gettrueiterator') {
                    return $queryExpression->getValue();
                }
            }
        }

        return $queryExpression;
    }

    public function saveResults(O\Expression $expression, $results)
    {
        $this->storage->attach($this->removeGetTrueIteratorCall($expression), $results);
    }

    public function clearResults()
    {
        $this->storage = new \SplObjectStorage();
    }

    public function removeResults(O\Expression $queryExpression)
    {
        unset($this->storage[$queryExpression]);
    }

    public function tryComputeResults(O\Expression $queryExpression, &$results)
    {
        if (isset($this->storage[$queryExpression])) {
            $results = $this->storage[$queryExpression];

            return true;
        }

        $foundApplicableResults = false;

        //Searches the query expression tree and checks if any parent expressions have saved results
        //If so, the expression tree is updated with a Traversable implementation with the saved results
        $applicableScopeFinder =
                function (O\Expression $expression, O\ExpressionWalker $self) use (&$foundApplicableResults) {
                    if (isset($this->storage[$expression])) {
                        $foundApplicableResults = true;

                        return O\Expression::value($this->newTraversable($this->storage[$expression]));
                    }

                    if ($expression instanceof O\ValueExpression) {
                        return $expression;
                    }

                    /** @var $expression O\TraversalExpression */

                    return $expression->updateValue($self->walk($expression->getValue()));
                };

        $traversalWalker = new O\DynamicExpressionWalker([
                O\TraversalExpression::getType() => $applicableScopeFinder,
                O\ValueExpression::getType()     => $applicableScopeFinder
        ]);

        $remainingQueryExpression = $traversalWalker->walk($queryExpression);

        //If found applicable results, execute the updated expression tree against the Traversable
        //implementation to compute the result of the query.
        $results = $foundApplicableResults ? $remainingQueryExpression->evaluate() : null;

        return $foundApplicableResults;
    }

    public function computeResults(O\Expression $expression)
    {
        if ($this->tryComputeResults($expression, $results)) {
            return $results;
        }

        throw new PinqException(
                'Could not compute query results: no applicable saved results found for the query expression, %s',
                $expression->compileDebug());
    }

    protected function newTraversable($values)
    {
        $factory = $this->traversableFactory;

        return $factory($values);
    }
}
