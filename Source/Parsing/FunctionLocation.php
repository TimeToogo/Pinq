<?php

namespace Pinq\Parsing;

/**
 * Implementation of the function location interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionLocation implements IFunctionLocation
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var int
     */
    protected $startLine;

    /**
     * @var int
     */
    protected $endLine;

    /**
     * @var string|null
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $hash;

    public function __construct(
            $filePath,
            $namespace,
            $startLine,
            $endLine
    ) {
        $this->filePath  = $filePath;
        $this->namespace = $namespace;
        $this->startLine = $startLine;
        $this->endLine   = $endLine;

        $this->hash = implode(
                '-',
                [
                        $filePath,
                        $namespace,
                        $startLine,
                        $endLine,
                ]
        );
    }

    /**
     * Creates a function location instance from the supplied reflection.
     *
     * @param \ReflectionFunctionAbstract $reflection
     *
     * @return self
     */
    public static function fromReflection(\ReflectionFunctionAbstract $reflection)
    {
        if ($reflection instanceof \ReflectionFunction) {
            $namespace = $reflection->getNamespaceName();
        } elseif ($reflection instanceof \ReflectionMethod) {
            $namespace = $reflection->getDeclaringClass()->getNamespaceName();
        } else {
            $namespace = null;
        }

        return new self(
                $reflection->getFileName(),
                $namespace,
                $reflection->getStartLine(),
                $reflection->getEndLine());
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function inNamespace()
    {
        return $this->namespace !== null;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getStartLine()
    {
        return $this->startLine;
    }

    public function getEndLine()
    {
        return $this->endLine;
    }

    public function getHash()
    {
        return $this->hash;
    }
}
