<?php

namespace Pinq\Tests\Integration\Parsing;

use \Pinq\Parsing;
use \Pinq\Parsing\IParser;
use \Pinq\Expressions as O;

abstract class ParserTest extends \Pinq\Tests\PinqTestCase
{
    private $Implementations;
    
    /**
     * @var IParser
     */
    private $CurrentImplementation;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->Implementations = $this->Implementations();
        
        $this->CurrentImplementation = isset($data[0]) ? $data[0] : null;
    }
    
    protected function Implementations()
    {
        return [
            new Parsing\PHPParser\Parser(),
        ];
    }

    final public function Parsers() 
    {
        return array_map(function ($I) { return [$I]; }, $this->Implementations);
    }
    
    final protected function AssertParsedAs(callable $Function, array $Expressions) 
    {
        if($this->CurrentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }
        
        $this->assertEquals($Expressions,
                $this->CurrentImplementation->Parse(
                        is_array($Function) ? new \ReflectionMethod($Function[0], $Function[1]) : new \ReflectionFunction($Function)));
    }
    
    protected static function Variable($Name)
    {
        return O\Expression::Variable(O\Expression::Value($Name));
    }
    
}
