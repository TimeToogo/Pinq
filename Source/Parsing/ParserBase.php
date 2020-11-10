<?php

namespace Pinq\Parsing;

/**
 * Base class for a function parser
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ParserBase implements IParser
{
    public function getReflection(callable $function)
    {
        return FunctionReflection::fromCallable($function);
    }

    final public function parse(IFunctionReflection $reflection)
    {
        $innerReflection = $reflection->getInnerReflection();
        if (!$innerReflection->isUserDefined()) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: function is not user defined',
                    $innerReflection->getName());
        }

        $filePath = $reflection->getLocation()->getFilePath();

        if (!is_readable($filePath)) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: \'%s\' is not a valid accessible file',
                    $innerReflection->getName(),
                    $filePath);
        }

        // try {
            return $this->parseFunction($reflection, $filePath);
        // } catch (ASTException $astException) {
        //     throw InvalidFunctionException::invalidFunctionMessage(
        //             $astException->getMessage(),
        //             $innerReflection
        //     );
        // }
    }

    /**
     * @param IFunctionReflection $reflection
     * @param string              $filePath
     *
     * @return IFunctionStructure
     */
    abstract protected function parseFunction(IFunctionReflection $reflection, $filePath);
}
