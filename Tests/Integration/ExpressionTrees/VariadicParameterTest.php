<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class VariadicParameterTest extends InterpreterTest
{
    /**
     * @var VariadicParameters
     */
    protected $parameters;

    protected function setUp(): void
    {
        if (version_compare(PHP_VERSION, '5.6.0-alpha3', '>=')) {
            $this->parameters = new VariadicParameters();
        } else {
            $this->markTestSkipped('Requires >=PHP 5.6');
        }
    }

    /**
     * @dataProvider interpreters
     */
    public function testSimpleVariadic()
    {
        $valueSet = [
            [1, 2, 3, 4, 5],
            ['abcde'],
            ['1', 'abcde', null, true],
            [null, new \stdClass()]
        ];
        $this->assertRecompilesCorrectly([$this->parameters, 'simpleVariadic'], $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testOnlyArraysByRef()
    {
        $array1 = [];
        $array2 = [1,2,3];
        $array3 = [[[[2]]]];
        $valueSet = [
                [&$array1],
                [&$array2, &$array3, &$array1],
        ];
        $this->assertRecompilesCorrectly([$this->parameters, 'onlyArraysByRef'], $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testArgumentUnpacking()
    {
        $valueSet = [
                ['strlen', ['abcdef']],
                ['strlen', ['123']],
                ['substr', ['abcdef', 2]],
                ['substr', ['abcdef', 3, 4]],
                ['substr', ['abcdef', -3, 2]],
        ];
        $this->assertRecompilesCorrectly([$this->parameters, 'argumentUnpacking'], $valueSet);
    }
}
