<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Parsing;

class ControlStructureParserTest extends ParserTest
{
    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithIfStatement()
    {
        $function =
                function () {
                    if (true) {

                    } else {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithForLoop()
    {
        $function =
                function () {
                    for (;;) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithForeachLoop()
    {
        $function =
                function () {
                    foreach ($i as $i) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithWhileLoop()
    {
        $function =
                function () {
                    while (true) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithDoWhileLoop()
    {
        $function =
                function () {
                    do {

                    } while (true);
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithGotoStatement()
    {
        $function =
                function () {
                    goto Bed;
                    Bed:
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithSwitchStatement()
    {
        $function =
                function () {
                    switch (true) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     * @expectedException Pinq\Parsing\InvalidFunctionException
     */
    public function testThrowsExceptionWithTryCatchStatement()
    {
        $function =
                function () {
                    try {

                    } catch (\Exception $exception) {

                    }
                };
        $this->assertParsedAs($function, []);
    }
}
