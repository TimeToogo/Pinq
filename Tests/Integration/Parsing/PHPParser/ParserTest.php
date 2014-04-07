<?php

namespace Pinq\Tests\Integration\Parsing\PHPParser;

use \Pinq\Parsing;

class ParserTest extends \Pinq\Tests\Integration\Parsing\SimpleParserTest
{
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName, [new Parsing\PHPParser\Parser()]);
    }
}
