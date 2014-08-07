<?php

namespace Pinq\Parsing;

/**
 * Implementation of the function location inteface.
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
            $startLine,
            $endLine
    ) {
        $this->filePath  = $filePath;
        $this->startLine = $startLine;
        $this->endLine   = $endLine;

        $this->hash = implode(
                '-',
                [
                        $filePath,
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
        return new self(
                $reflection->getFileName(),
                $reflection->getStartLine(),
                $reflection->getEndLine());
    }

    public function getFilePath()
    {
        return $this->filePath;
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
