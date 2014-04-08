<?php

namespace Pinq\Tests\Integration\Parsing\PHPParser;

use \Pinq\Parsing;

class ComplexParserTest extends \Pinq\Tests\Integration\Parsing\ComplexParserTest
{
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName, [new Parsing\PHPParser\Parser()]);
    }
}
