<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Parsing;
use Pinq\Parsing\IParser;
use Pinq\Expressions as O;

abstract class ParserTest extends \Pinq\Tests\PinqTestCase
{
    private $implementations;

    /**
     * @var IParser
     */
    private $currentImplementation;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->implementations = $this->implementations();
        $this->currentImplementation = isset($data[0]) ? $data[0] : null;
    }

    protected function implementations()
    {
        return [new Parsing\PhpParser\Parser()];
    }

    final public function parsers()
    {
        return array_map(function ($i) {
            return [$i];
        }, $this->implementations);
    }

    final protected function assertParsedAs(callable $function, array $expressions)
    {
        if ($this->currentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }

        $this->assertEquals(
                $expressions,
                $this->currentImplementation->parse($this->currentImplementation->getReflection($function))->getBodyExpressions());
    }

    final protected function assertReturn(callable $function, O\Expression $valueExpression)
    {
        return $this->assertParsedAs($function, [O\Expression::returnExpression($valueExpression)]);
    }

    protected static function variable($name)
    {
        return O\Expression::variable(O\Expression::value($name));
    }
}
