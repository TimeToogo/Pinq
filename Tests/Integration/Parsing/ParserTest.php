<?php

namespace Pinq\Tests\Integration\Parsing;

use \Pinq\Parsing\IParser;
use \Pinq\Expressions as O;

abstract class ParserTest extends \Pinq\Tests\PinqTestCase
{
    private $Implementations;
    
    /**
     * @var IParser
     */
    private $CurrentImplementation;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $Implementations)
    {
        parent::__construct($name, $data, $dataName);
        $this->Implementations = $Implementations;
        
        $this->CurrentImplementation = isset($data[0][0]) ? $data[0][0] : null;
    }

    final public function Parsers() 
    {
        return [
            array_map(function($I) { return [$I]; }, $this->Implementations),
        ];
    }
    
    final protected function AssertParsedAs(callable $Function, array $Expressions) 
    {
        $this->assertEquals($Expressions,
                $this->CurrentImplementation->Parse(
                        is_array($Function) ? new \ReflectionMethod($Function[0], $Function[1]) : new \ReflectionFunction($Function)));
    }
    
}
