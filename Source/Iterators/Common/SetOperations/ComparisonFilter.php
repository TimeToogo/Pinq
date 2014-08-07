<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;

/**
 * Base class for a set filter based on another set
 * of values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ComparisonFilter extends SetFilter
{
    /**
     * @var \Traversable
     */
    private $comparisonValuesIterator;

    public function __construct(IIteratorScheme $scheme, \Traversable $comparisonValuesIterator)
    {
        parent::__construct($scheme);
        $this->comparisonValuesIterator = $comparisonValuesIterator;
    }

    public function initialize()
    {
        $this->set = $this->scheme->createSet($this->comparisonValuesIterator);
    }
}
