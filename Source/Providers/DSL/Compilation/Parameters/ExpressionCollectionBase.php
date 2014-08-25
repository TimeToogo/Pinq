<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;

/**
 * Base class of the expression collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ExpressionCollectionBase implements \Countable
{
    /**
     * @var \SplObjectStorage|O\Expression[]|ExpressionParameter[]
     */
    protected $expressions;

    public function __construct(\SplObjectStorage $expressions)
    {
        $this->expressions = $expressions;
    }

    public function count()
    {
        return count($this->expressions);
    }

    /**
     * @return O\Expression[]
     */
    public function getExpressions()
    {
        return iterator_to_array($this->expressions);
    }

    /**
     * @param O\Expression $expression
     *
     * @return mixed
     */
    public function getData(O\Expression $expression)
    {
        return $this->expressions[$expression]->getData();
    }

    /**
     * @param O\Expression $expression
     *
     * @return ExpressionParameter
     */
    public function getExpressionParameter(O\Expression $expression)
    {
        return $this->expressions[$expression];
    }

    /**
     * @return ExpressionParameter[]
     */
    public function getExpressionParameters()
    {
        $parameters = [];
        foreach ($this->expressions as $expression) {
            $parameters[] = $this->expressions->getInfo();
        }

        return $parameters;
    }
}