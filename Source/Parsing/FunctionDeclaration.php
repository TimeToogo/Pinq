<?php

namespace Pinq\Parsing;

/**
 * Implementation of the function declaration interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionDeclaration implements IFunctionDeclaration
{
    /**
     * @var string|null
     */
    protected $namespace;

    /**
     * @var string|null
     */
    protected $class;

    /**
     * @var string|null
     */
    protected $trait;

    /**
     * @var string|null
     */
    protected $function;

    /**
     * @var string|null
     */
    protected $closureNestingLevel;

    public function __construct($namespace, $class, $trait, $function, $closureNestingLevel)
    {
        $this->namespace           = $namespace;
        $this->class               = $class;
        $this->trait               = $trait;
        $this->function            = $function;
        $this->closureNestingLevel = $closureNestingLevel;
    }

    public function isWithinNamespace()
    {
        return $this->namespace !== null;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function isWithinClass()
    {
        return $this->class !== null;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function isWithinTrait()
    {
        return $this->trait !== null;
    }

    public function getTrait()
    {
        return $this->trait;
    }

    public function isWithinFunction()
    {
        return $this->function !== null;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function getClosureNestingLevel()
    {
        return $this->closureNestingLevel;
    }

}
