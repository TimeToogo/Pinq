<?php 

namespace Pinq\Providers\Collection;

use Pinq\Queries\Operations;

/**
 * Evaluates the operations on the supplied collection instance
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OperationEvaluator extends Operations\OperationVisitor
{
    /**
     * @var \Pinq\ICollection
     */
    private $collection;
    
    public function __construct(\Pinq\ICollection $collection)
    {
        $this->collection = $collection;
    }
    
    public function visitApply(Operations\Apply $operation)
    {
        $this->collection->apply($operation->getFunctionExpressionTree());
    }
    
    public function visitAddValues(Operations\AddValues $operation)
    {
        $this->collection->addRange($operation->getValues());
    }
    
    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        $this->collection->removeRange($operation->getValues());
    }
    
    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        $this->collection->removeWhere($operation->getFunctionExpressionTree());
    }
    
    public function visitClear(Operations\Clear $operation)
    {
        $this->collection->clear();
    }
    
    public function visitSetIndex(Operations\SetIndex $operation)
    {
        $this->collection[$operation->getIndex()] = $operation->getValue();
    }
    
    public function visitUnsetIndex(Operations\UnsetIndex $operation)
    {
        unset($this->collection[$operation->getIndex()]);
    }
}