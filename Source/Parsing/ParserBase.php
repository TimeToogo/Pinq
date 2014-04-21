<?php

namespace Pinq\Parsing;

use \Pinq\Expressions\Expression;

/**
 * Base class for a function parser
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ParserBase implements IParser
{
    final public function Parse(\ReflectionFunctionAbstract $Reflection)
    {
        if (!$Reflection->isUserDefined()) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: Function is not user defined',
                    $Reflection->getName());
        }

        $FileName = $Reflection->getFileName();
        if (!is_readable($FileName)) {
            throw new InvalidFunctionException(
                    'Cannot parse function %s: \'%s\' is not a valid accessable file',
                    $Reflection->getName(),
                    $FileName);
        }
        
        try {
            return $this->ParseFunction($Reflection, $FileName);
        }
        catch (ASTException $ASTException) {
            throw InvalidFunctionException::InvalidFunctionMessage($ASTException->getMessage(), $Reflection);
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $Reflection
     * @param string $FileName
     * @return Expression[]
     */
    abstract protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName);
}
