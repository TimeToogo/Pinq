<?php

namespace Pinq;

class JoiningOnTraversable implements IJoiningOnTraversable
{
    /**
     * @var \Traversable
     */
    private $OuterValues;
    
    /**
     * @var \Traversable
     */
    private $InnerValues;
    
    /**
     * @var boolean
     */
    private $IsGroupJoin;
    
    /**
     * @param boolean $IsGroupJoin
     */
    public function __construct(\Traversable $OuterValues, \Traversable $InnerValues, $IsGroupJoin)
    {
        $this->OuterValues = $OuterValues;
        $this->InnerValues = $InnerValues;
        $this->IsGroupJoin = $IsGroupJoin;
    }

    public function On(callable $JoiningOnFunction)
    {
        return new JoiningToTraversable(function (callable $JoiningFunction) use ($JoiningOnFunction) {
            if($this->IsGroupJoin) {
                return new Iterators\CustomGroupJoinIterator(
                        $this->OuterValues, 
                        $this->InnerValues,
                        $JoiningOnFunction,
                        $JoiningFunction);
            }
            else {
                return new Iterators\CustomJoinIterator(
                        $this->OuterValues, 
                        $this->InnerValues,
                        $JoiningOnFunction, 
                        $JoiningFunction);
            }
        });
    }

    public function OnEquality(callable $OuterKeyFunction, callable $InnerKeyFunction)
    {
        return new JoiningToTraversable(function (callable $JoiningFunction) use ($OuterKeyFunction, $InnerKeyFunction) {
            if($this->IsGroupJoin) {
                return new Iterators\EqualityGroupJoinIterator(
                        $this->OuterValues, 
                        $this->InnerValues,
                        $OuterKeyFunction, 
                        $InnerKeyFunction,
                        $JoiningFunction);
            }
            else {
                return new Iterators\EqualityJoinIterator(
                        $this->OuterValues, 
                        $this->InnerValues,
                        $OuterKeyFunction, 
                        $InnerKeyFunction,
                        $JoiningFunction);
            }
        });
        
    }
}
