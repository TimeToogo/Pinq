<?php

namespace Pinq\Parsing;

use \Pinq\Expressions as O;

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

        return $this->ParseFunction($Reflection, $FileName);
    }

    /**
     * @param string $FileName
     */
    abstract protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName);
}
