<?php

namespace Pinq\Analysis\TypeOperations;

use Pinq\Analysis\IIndexer;
use Pinq\Analysis\ITypeSystem;

/**
 * Implementation of the indexer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Indexer extends TypeOperation implements IIndexer
{
    public function __construct(ITypeSystem $typeSystem, $sourceType, $returnType)
    {
        parent::__construct($typeSystem, $sourceType, $returnType);
    }

    public function getReturnTypeOfIndex($index)
    {
        return $this->getReturnType();
    }
}
