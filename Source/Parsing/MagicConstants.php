<?php

namespace Pinq\Parsing;

/**
 * Implementation of the magic constants interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MagicConstants implements IMagicConstants
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var string|null
     */
    private $class;

    /**
     * @var string|null
     */
    private $trait;

    /**
     * @var string|null
     */
    private $function;

    /**
     * @var string|null
     */
    private $functionInClosure;

    /**
     * @var string|null
     */
    private $method;

    /**
     * @var string|null
     */
    private $methodInClosure;

    public function __construct(
            $directory,
            $file,
            $namespace,
            $class,
            $trait,
            $function,
            $functionInClosure,
            $method,
            $methodInClosure
    ) {
        $this->directory         = $directory;
        $this->file              = $file;
        $this->namespace         = $namespace;
        $this->class             = $class;
        $this->trait             = $trait;
        $this->function          = $function;
        $this->method            = $method;
        $this->functionInClosure = $functionInClosure;
        $this->methodInClosure   = $methodInClosure;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getTrait()
    {
        return $this->trait;
    }

    public function getFunction($isWithinClosure)
    {
        return $isWithinClosure ? $this->functionInClosure : $this->function;
    }

    public function getMethod($isWithinClosure)
    {
        return $isWithinClosure ? $this->methodInClosure : $this->method;
    }
}
