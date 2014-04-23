<?php 

namespace Pinq\Queries\Operations;

/**
 * Base class for an operation query using a supplied index
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IndexOperation extends Operation
{
    /**
     * @var mixed
     */
    private $index;
    
    public function __construct($index)
    {
        $this->index = $index;
    }
    
    public final function getIndex()
    {
        return $this->index;
    }
}