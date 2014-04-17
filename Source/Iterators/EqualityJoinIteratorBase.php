<?php

namespace Pinq\Iterators;

abstract class EqualityJoinIteratorBase extends JoinIteratorBase
{    
    /**
     * @var callable
     */
    private $OuterKeyFunction;
    
    /**
     * @var callable
     */
    private $InnerKeyFunction;
    
    /**
     * @var Utilities\Lookup
     */
    private $InnerKeyLookup;
    
    public function __construct(
            \Traversable $OuterIterator,
            \Traversable $InnerIterator,
            callable $OuterKeyFunction,
            callable $InnerKeyFunction,
            callable $JoiningFunction)
    {
        parent::__construct($OuterIterator, $InnerIterator, $JoiningFunction);
        $this->OuterKeyFunction = $OuterKeyFunction;
        $this->InnerKeyFunction = $InnerKeyFunction;
    }
    
    final protected function Initialize()
    {
        $this->InnerKeyLookup = Utilities\Lookup::FromGroupingFunction($this->InnerKeyFunction, $this->InnerIterator);
    }
    
    final protected function GetInnerGroupIterator($OuterValue)
    {
        $OuterKeyFunction = $this->OuterKeyFunction;
        $OuterKey = $OuterKeyFunction($OuterValue);
        
        if($this->InnerKeyLookup->Contains($OuterKey)) {
            $CurrentInnerGroup = $this->InnerKeyLookup->Get($OuterKey);
        }
        else {
            $CurrentInnerGroup = [];
        }
        
        return $this->GetInnerGroupValueIterator($CurrentInnerGroup);
    }
    
    protected abstract function GetInnerGroupValueIterator(array $InnerGroup);
}
