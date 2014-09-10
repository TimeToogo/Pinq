<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\ITraversable;
use Pinq\ITraversable as AliasedTraversable;
use Pinq\Traversable;

class BasicInterpreterTest extends InterpreterTest
{
    /**
     * @dataProvider interpreters
     */
    public function testEmptyFunction()
    {
        $this->assertRecompilesCorrectly(function () {}, []);
    }

    /**
     * @dataProvider interpreters
     */
    public function testBinaryOperations()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2],  [5], [500]];

        $this->assertRecompilesCorrectly(function ($i) { return $i + 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i - 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i * 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i / 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i % 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i & 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i | 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i << 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i >> 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i && 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i || 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i === 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i !== 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i == 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i != 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i > 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i >= 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i < 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i <= 1; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i . 1; }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testInstanceOf()
    {
        $valueSet = [[null], [true], [new \DateTime()], [new \stdClass()], [new \DateTime()]];

        $this->assertRecompilesCorrectly(function ($i) { return $i instanceof \DateTimeInterface; }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUnaryOperations()
    {
        $valueSet = [[1], [0]];

        $this->assertRecompilesCorrectly(function ($i) { return -$i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return !$i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return ~$i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return +$i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i++; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i--; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return ++$i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return --$i; }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testCastOperations()
    {
        $valueSet = [[1], [0], [true], [false]];

        $this->assertRecompilesCorrectly(function ($i) { return (string) $i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return (bool) $i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return (int) $i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return (array) $i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return (object) $i; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return (double) $i; }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testLanguageConstructs()
    {
        $valueSet = [[''], ['1'], ['test'], [null], [true], [false], [['dsffd']], [[]], [0]];

        $this->assertRecompilesCorrectly(function ($i) { return isset($i); }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return empty($i); }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testTernary()
    {
        $valueSet = [[1], [0], [-1], [-5], [5], [-500], [500]];

        $this->assertRecompilesCorrectly(function ($i) { return $i > 0 ? 5 : -50; }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return $i > 0 ? : -50; }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testObjectMethodCall()
    {
        $valueSet = [[new \DateTime()], [new \DateTime('-3 days')], [new \DateTime('+2 weeks')]];

        $this->assertRecompilesCorrectly(
                function (\DateTime $i) {
                    return $i->format(\DateTime::ATOM);
                },
                $valueSet);

        $this->assertRecompilesCorrectly(
                function (\DateTime $i) {
                    return $i->getTimestamp();
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testObjectField()
    {
        $valueSet = [
            [(object) ['prop' => null, 'foo' => null]],
            [(object) ['prop' => true, 'foo' => false]],
            [(object) ['prop' => 'fdsfdsdf', 'foo' => 'fewfew']]
        ];

        $this->assertRecompilesCorrectly(
                function (\stdClass $i) {
                    return $i->prop;
                },
                $valueSet);

        $this->assertRecompilesCorrectly(
                function (\stdClass $i) {
                    return $i->foo;
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testValueIndex()
    {
        $valueSet = [
            [['prop' => null, 'foo' => null]],
            [['prop' => true, 'foo' => false]],
            [['prop' => 'fdsfdsdf', 'foo' => 'fewfew']]
        ];

        $this->assertRecompilesCorrectly(
                function (array $i) {
                    return $i['prop'];
                },
                $valueSet);

        $this->assertRecompilesCorrectly(
                function (array $i) {
                    return $i['foo'];
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testFunctionCall()
    {
        $valueSet = [
            [''],
            ['1'],
            ['test'],
            ['fooo'],
            ['geges ges  gse e'],
            ['striiiiiiiiiiing']
        ];

        $this->assertRecompilesCorrectly(function ($i) { return strlen($i); }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return str_split($i); }, $valueSet);
        $this->assertRecompilesCorrectly(function ($i) { return  explode(' ', $i); }, $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testStaticMethodCall()
    {
        $valueSet = [[range(0, 10)], [range(-100, 100)], [[1, '2', 3, '4', 5, '6', 7, '8', 9, '0']]];

        $this->assertRecompilesCorrectly(
                function (array $i) {
                    return \SplFixedArray::fromArray($i);
                },
                $valueSet);
    }

    public static $field = [1, 2, 3];

    /**
     * @dataProvider interpreters
     */
    public function testStaticField()
    {
        $valueSet = [];

        $this->assertRecompilesCorrectly(
                function () {
                    return BasicInterpreterTest::$field;
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedTypeHintClass()
    {
        $valueSet = [[Traversable::from([])]];

        $this->assertRecompilesCorrectly(
                function (ITraversable $traversable) {
                    return $traversable;
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testAliasedTypeHintClass()
    {
        $valueSet = [[Traversable::from([])]];

        $this->assertRecompilesCorrectly(
                function (AliasedTraversable $traversable) {
                    return $traversable;
                },
                $valueSet);
    }
}
