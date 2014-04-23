<?php

namespace Pinq\Parsing\PHPParser;

/**
 * Placeholder node for a resolved value
 *
 * @property mixed $value The resolved value
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PHPParserResolvedValueNode extends \PHPParser_Node_Expr
{
    public function __construct(&$value)
    {
        parent::__construct(['value' => &$value], []);
    }
}
