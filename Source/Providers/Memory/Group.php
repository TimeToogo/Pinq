<?php

namespace Pinq\Providers\Memory;

abstract class Group implements \Pinq\IGroup
{
    protected $Values;

    public function __construct(array &$Values)
    {
        $this->Values =& $Values;
    }

    final public function &GetValues() {
        return $this->Values;
    }

    final public function Scope(array $Values)
    {
        $Clone = clone $this;
        $Clone->Values =& $Values;

        return $Clone;
    }

    final public function Count()
    {
        return count($this->Values);
    }

    final public function Exists()
    {
        return !empty($this->Values);
    }

    final public function First()
    {
        return empty($this->Values) ? null : reset($this->Values);
    }

    final public function Aggregate(callable $Function)
    {
        return array_reduce($this->Values, $Function, null);
    }
}
