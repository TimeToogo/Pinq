<?php

namespace Pinq\Providers\Generators;

use \Pinq\Providers\Arrays;

class Provider extends Arrays\Provider
{
    
    public function __construct(array &$Values)
    {
        \Pinq\Providers\Memory\Provider::__construct($Values, new QueryStreamEvaluatorVisitor());
    }
    
    protected function NewScope(array $Values)
    {
        return new self($Values);
    }
    
    protected function Map(callable $Function = null)
    {
        if ($Function === null) {
            return $this->Values;
        } 
        else {
            return array_map($Function, $this->Values);
        }
    }

    public function Contains($Value)
    {
        return in_array($Value, $this->Values, true);
    }

    public function Maximum(callable $Function = null)
    {
        return max($this->Map($Function));
    }

    public function Minimum(callable $Function = null)
    {
        return min($this->Map($Function));
    }

    public function Sum(callable $Function = null)
    {
        return array_sum($this->Map($Function));
    }

    public function Average(callable $Function = null)
    {
        return $this->Sum($Function) / $this->Count();
    }

    public function All(callable $Function = null)
    {
        foreach ($this->Map($Function) as $Value) {
            if (!$Value) {
                return false;
            }
        }

        return true;
    }

    public function Any(callable $Function = null)
    {
        foreach ($this->Map($Function) as $Value) {
            if ($Value) {
                return true;
            }
        }

        return false;
    }

    public function Implode($Delimiter, callable $Function = null)
    {
        return implode($Delimiter, $this->Map($Function));
    }
}
