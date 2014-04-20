<?php

namespace Pinq\Parsing;

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
     * @param string $FileName
     */
    abstract protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName);
}
