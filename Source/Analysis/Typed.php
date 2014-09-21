<?php

namespace Pinq\Analysis;

/**
 * Base class for classes using the type system.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Typed implements ITyped
{
    /**
     * @var ITypeSystem
     */
    protected $typeSystem;

    public function __construct(ITypeSystem $typeSystem)
    {
        $this->typeSystem = $typeSystem;
    }

    public function getTypeSystem()
    {
        return $this->typeSystem;
    }
}
