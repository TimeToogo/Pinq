<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

abstract class ExpressionTest extends \Pinq\Tests\PinqTestCase
{
    public function expressions()
    {
        return [
                [O\Expression::arrayExpression([])],
                [O\Expression::arrayItem(null, O\Expression::value(0), false)],
                [O\Expression::assign(
                        O\Expression::value(0),
                        O\Operators\Assignment::EQUAL,
                        O\Expression::value(0))],
                [O\Expression::binaryOperation(
                        O\Expression::value(0),
                        O\Operators\Binary::ADDITION,
                        O\Expression::value(0))],
                [O\Expression::unaryOperation(
                        O\Operators\Unary::PLUS,
                        O\Expression::value(0))],
                [O\Expression::cast(O\Operators\Cast::STRING, O\Expression::value(0))],
                [O\Expression::closure(false, false, [], [], [])],
                [O\Expression::closureUsedVariable('var')],
                [O\Expression::emptyExpression(O\Expression::value(0))],
                [O\Expression::field(O\Expression::value(0), O\Expression::value(0))],
                [O\Expression::functionCall(O\Expression::value(0))],
                [O\Expression::index(O\Expression::value(0), O\Expression::value(0))],
                [O\Expression::invocation(O\Expression::value(0))],
                [O\Expression::issetExpression([O\Expression::value(0)])],
                [O\Expression::unsetExpression([O\Expression::value(0)])],
                [O\Expression::methodCall(
                        O\Expression::value(0),
                        O\Expression::value(0))],
                [O\Expression::newExpression(O\Expression::value(0))],
                [O\Expression::parameter('')],
                [O\Expression::argument(O\Expression::value(0))],
                [O\Expression::returnExpression()],
                [O\Expression::staticMethodCall(
                        O\Expression::value(0),
                        O\Expression::value(0))],
                [O\Expression::staticField(
                        O\Expression::value(0),
                        O\Expression::value(0))],
                [O\Expression::ternary(
                        O\Expression::value(0),
                        null,
                        O\Expression::value(0))],
                [O\Expression::throwExpression(O\Expression::value(0))],
                [O\Expression::value(0)],
                [O\Expression::variable(O\Expression::value(0))],
                [O\Expression::constant('foo')],
                [O\Expression::classConstant(O\Expression::value(0), 'foo')],
        ];
    }
}
