<?php

namespace Pinq;

class Collection extends Queryable implements ICollection
{
    private $Values;
    
    public function __construct(array $Values = [])
    {
        //parent::__construct(new Providers\Generators\Provider($Values));
        parent::__construct(new Providers\Arrays\Provider($Values));
        $this->Values =& $Values;
    }
    
    protected function OnNewQueryScope()
    {
        $this->Values =& $this->Provider->Retrieve();
    }
    
    public function AsCollection()
    {
        return $this;
    }
    
    public function AsTraversable()
    {
        return $this;
    }
    
    public function Apply(callable $Function)
    {
        $this->LoadQueryScope();
        array_walk($this->Values, $Function);
    }

    public function AddRange($Values)
    {
        $this->LoadQueryScope();
        foreach ($Values as $Value) {
            $this->Values[] = $Value;
        }
    }

    public function RemoveRange($Values)
    {
        $this->LoadQueryScope();
        foreach ($Values as $Value) {
            $Key = array_search($Value, $this->Values, true);
            if($Key !== false) {
                unset($this->Values[$Key]);
            }
        }
    }

    public function RemoveWhere(callable $Predicate)
    {
        $this->LoadQueryScope();
        foreach ($this->Values as $Key => $Value) {
            if($Predicate($Value, $Key)) {
                unset($this->Values[$Key]);
            }
        }
    }

    public function offsetExists($Index)
    {
        $this->LoadQueryScope();
        return isset($this->Values[$Index]);
    }

    public function offsetGet($Index)
    {
        $this->LoadQueryScope();
        return $this->Values[$Index];
    }

    public function offsetSet($Index, $Value)
    {
        $this->LoadQueryScope();
        $this->Values[$Index] = $Value;
    }

    public function offsetUnset($Index)
    {
        $this->LoadQueryScope();
        unset($this->Values[$Index]);
    }

}
