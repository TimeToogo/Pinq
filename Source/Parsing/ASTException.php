<?php

namespace Pinq\Parsing;

class ASTException extends InvalidFunctionException
{
    const IfStatement = 'if(...)';
    const WhileLoop = 'while(...)';
    const DoWhileLoop = 'do...while(...)';
    const ForLoop = 'for(...)';
    const ForeachLoop = 'foreach(...)';
    const SwitchStatement = 'switch(...)';
    const GotoStatement = 'goto ...;';
    const TryCatchStatement = 'try ... catch(...)';
    
    public static function ContainsControlStructure($ControlStrucuture, $LineNumber) 
    {
        return new self('Contains control structure \'%s\' on line %d', $ControlStrucuture, $LineNumber);
    }
}
