<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Parsing;
use Pinq\Parsing\InvalidFunctionException;

class ControlStructureParserTest extends ParserTest
{
    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithIfStatement()
    {
        $this->expectException(InvalidFunctionException::class);
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
     */
    public function testThrowsExceptionWithForLoop()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    for (;;) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithForeachLoop()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    foreach ($i as $i) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithWhileLoop()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    while (true) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithDoWhileLoop()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    do {

                    } while (true);
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithGotoStatement()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    goto Bed;
                    Bed:
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithSwitchStatement()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    switch (true) {

                    }
                };
        $this->assertParsedAs($function, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowsExceptionWithTryCatchStatement()
    {
        $this->expectException(InvalidFunctionException::class);
        $function =
                function () {
                    try {

                    } catch (\Exception $exception) {

                    }
                };
        $this->assertParsedAs($function, []);
    }
}
