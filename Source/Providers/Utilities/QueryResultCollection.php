<?php

namespace Pinq\Providers\Utilities;

use Pinq\Expressions as O;
use Pinq\Traversable;

/**
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class QueryResultCollection
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
        $this->traversableFactory = $traversableFactory ? : Traversable::factory();
    }

    public function saveResults(O\Expression $expression, $results)
    {
        $this->storage->attach($expression, $results);
    }

    public function clearResults()
    {
        $this->storage = new \SplObjectStorage();
    }

    public function removeResults(O\Expression $expression)
    {
        unset($this->storage[$expression]);
    }

    public function tryComputeResults(O\Expression $expression, &$results)
    {
        if (isset($this->storage[$expression])) {
            $results = $this->storage[$expression];
            return true;
        }

        return $this->computeResults($expression, $results);
    }

    protected function computeResults(O\Expression $expression, &$results)
    {
        $foundApplicableResults = false;

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

        $remainingScopeExpression = $traversalWalker->walk($expression);

        $results = $foundApplicableResults ? $remainingScopeExpression->simplifyToValue() : null;
        return $foundApplicableResults;
    }

    protected function newTraversable($values)
    {
        $factory = $this->traversableFactory;
        return $factory($values);
    }
}