<?php

namespace Pinq\Parsing\PHPParser;

/**
 * @property mixed $Value The resolved value
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
