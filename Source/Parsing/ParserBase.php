<?php

namespace Pinq\Parsing;

use \Pinq\Expressions as O;

abstract class ParserBase implements IParser
{
    final public function Parse(\ReflectionFunctionAbstract $Reflection)
    {
        if (!$Reflection->isUserDefined()) {
            $ArgumentExpressions = [];
            foreach($Reflection->getParameters() as $Parameter) {
                $ArgumentExpressions[] = O\Expression::Variable(O\Expression::Value($Parameter->name));
            }
            
            return [
                O\Expression::ReturnExpression(
                        O\Expression::FunctionCall(
                                O\Expression::Value($Reflection->name)),
                                $ArgumentExpressions)
            ];
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

    abstract protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName);
}
