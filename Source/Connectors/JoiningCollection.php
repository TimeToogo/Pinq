<?php

namespace Pinq\Connectors;

use Pinq\ICollection;
use Pinq\Interfaces;
use Pinq\Iterators\IJoinIterator;

/**
 * Implements the filtering API for a join / group join collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoiningCollection extends JoiningTraversable implements Interfaces\IJoiningOnCollection
{
    /**
     * @var ICollection
     */
    private $collection;

    public function __construct(ICollection $collection, IJoinIterator $joinIterator, callable $collectionFactory)
    {
        parent::__construct($collection->getIteratorScheme(), $joinIterator, $collectionFactory);
        $this->collection = $collection;
    }

    public function apply(callable $applyFunction)
    {
        $this->joinIterator->walk($applyFunction);
    }
}
