<?php

namespace Pinq\Parsing\PHPParser;

/**
 * Placeholder node for a resolved value
 * 
 * @property mixed $Value The resolved value
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PHPParserResolvedValueNode extends \PHPParser_Node_Expr
{
    public function __construct(&$Value)
    {
        parent::__construct(
                [
                    'Value' => &$Value
                ],
                []);
    }
}
