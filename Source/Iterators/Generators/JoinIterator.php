<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IJoinToIterator;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class JoinIterator extends IteratorGenerator implements IJoinToIterator
{
    use Common\JoinIterator;

    /**
     * @var IGenerator
     */
    protected $outerIterator;

    /**
     * @var IGenerator
     */
    protected $innerIterator;

    public function __construct(IGenerator $outerIterator, IGenerator $innerIterator)
    {
        parent::__construct($outerIterator);
        self::__constructIterator();
        $this->outerIterator =& $this->iterator;
        $this->innerIterator = $innerIterator;
    }

    protected function beforeOuterLoopData()
    {
        return [];
    }

    public function walk(callable $function)
    {
        $data = $this->beforeOuterLoopData();
        foreach ($this->outerIterator as $outerKey => &$outerValue) {
            foreach ($this->innerGenerator($outerKey, $outerValue, $data) as $innerKey => &$innerValue) {
                $function($outerValue, $innerValue, $outerKey, $innerKey);
            }
        }
    }

    final protected function &iteratorGenerator(IGenerator $iterator)
    {
        $count = 0;

        $data = $this->beforeOuterLoopData();
        foreach ($this->outerIterator as $outerKey => $outerValue) {
            foreach($this->innerForeach($outerKey, $outerValue, $data, $count) as $key => $value) {
                yield $key => $value;
                unset($value);
            }
        }
    }

    protected function innerForeach($outerKey, $outerValue, array $data, &$count)
    {
        $projectionFunction = $this->projectionFunction;

        foreach ($this->innerGenerator($outerKey, $outerValue, $data) as $innerKey => $innerValue) {
            yield $count++ => $projectionFunction($outerValue, $innerValue, $outerKey, $innerKey);
        }
    }

    final protected function defaultIterator(IGenerator $iterator)
    {
        return $this->hasDefault ?
                new CoalesceIterator($iterator, $this->defaultValue, $this->defaultKey) : $iterator;
    }

    /**
     * @param mixed $outerKey
     * @param mixed $outerValue
     * @param array $outerData
     *
     * @return IGenerator
     */
    abstract protected function innerGenerator($outerKey, $outerValue, array $outerData);
}
