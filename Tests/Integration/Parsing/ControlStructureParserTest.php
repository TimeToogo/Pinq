<?php

namespace Pinq\Tests\Integration\Parsing;

use \Pinq\Parsing;

class ControlStructureParserTest extends ParserTest
{
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithIfStatement()
    {
        $Function = function () {
            if(true) {
                
            }
            else {
                
            }
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithForLoop()
    {
        $Function = function () {
            for (;;) {
                
            }
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithForeachLoop()
    {
        $Function = function () {
            foreach ($I as $I) {
                
            }
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithWhileLoop()
    {
        $Function = function () {
            while (true) {
                
            }
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithDoWhileLoop()
    {
        $Function = function () {
            do {
                
            } while (true);
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithGotoStatement()
    {
        $Function = function () {
            goto Bed;
            Bed:
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithSwitchStatement()
    {
        $Function = function () {
            switch (true) {
                
            }
        };
        
        $this->AssertParsedAs($Function, []);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithTryCatchStatement()
    {
        $Function = function () {
            try {
                
            } 
            catch (\Exception $Exception) {
                
            }
        };
        
        $this->AssertParsedAs($Function, []);
    }
}
