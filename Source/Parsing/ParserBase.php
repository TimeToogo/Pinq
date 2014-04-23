<?php

namespace Pinq\Parsing;

use Pinq\Expressions\Expression;

/**
 * Base class for a function parser
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ParserBase implements IParser
{
    final public function parse(\ReflectionFunctionAbstract $reflection)
    {
        if (!$reflection->isUserDefined()) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: Function is not user defined',
                    $reflection->getName());
        }

        $fileName = $reflection->getFileName();

        if (!is_readable($fileName)) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: \'%s\' is not a valid accessable file',
                    $reflection->getName(),
                    $fileName);
        }

        try {
            return $this->parseFunction($reflection, $fileName);
        } catch (ASTException $astException) {
            throw InvalidFunctionException::invalidFunctionMessage(
                    $astException->getMessage(),
                    $reflection);
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     * @param string $fileName
     * @return Expression[]
     */
    abstract protected function parseFunction(\ReflectionFunctionAbstract $reflection, $fileName);
}
