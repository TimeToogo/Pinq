<?php

namespace Pinq\Parsing;

/**
 * Exception for errors while converting the AST into
 * an expression tree
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ASTException extends InvalidFunctionException
{
    const IF_STATEMENT        = 'if(...)';
    const WHILE_LOOP          = 'while(...)';
    const DO_WHILE_LOOP       = 'do...while(...)';
    const FOR_LOOP            = 'for(...)';
    const FOREACH_LOOP        = 'foreach(...)';
    const SWITCH_STATEMENT    = 'switch(...)';
    const GOTO_STATEMENT      = 'goto ...;';
    const TRY_CATCH_STATEMENT = 'try ... catch(...)';

    public static function containsControlStructure($controlStrucuture, $lineNumber)
    {
        return new self(
                'Contains control structure \'%s\' on line %d',
                $controlStrucuture,
                $lineNumber);
    }
}
