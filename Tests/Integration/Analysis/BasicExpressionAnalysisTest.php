<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\INativeType;
use Pinq\Analysis\TypeData\DateTime;
use Pinq\IQueryable;
use Pinq\IRepository;
use Pinq\ITraversable;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BasicExpressionAnalysisTest extends ExpressionAnalysisTestCase
{
    protected static $field = true;

    public function testNativeTypes()
    {
        $this->assertReturnsNativeType(function () { ''; }, INativeType::TYPE_STRING);
        $this->assertReturnsNativeType(function () { 'abcef'; }, INativeType::TYPE_STRING);
        $this->assertReturnsNativeType(function () { 1; }, INativeType::TYPE_INT);
        $this->assertReturnsNativeType(function () { 133453; }, INativeType::TYPE_INT);
        $this->assertReturnsNativeType(function () { true; }, INativeType::TYPE_BOOL);
        $this->assertReturnsNativeType(function () { false; }, INativeType::TYPE_BOOL);
        $this->assertReturnsNativeType(function () { null; }, INativeType::TYPE_NULL);
        $this->assertReturnsNativeType(function () { 3.14; }, INativeType::TYPE_DOUBLE);
        $this->assertReturnsNativeType(function () { []; }, INativeType::TYPE_ARRAY);
        $this->assertReturnsNativeType(function () { [1,2 , 'ddsad' => 2, 'abc']; }, INativeType::TYPE_ARRAY);
    }

    public function testNativeTypesWithVariables()
    {
        $values = [
                INativeType::TYPE_STRING  => 'abc',
                INativeType::TYPE_INT     => -34,
                INativeType::TYPE_BOOL => true,
                INativeType::TYPE_DOUBLE  => -4.2454,
                INativeType::TYPE_NULL    => null,
                INativeType::TYPE_ARRAY   => [222, '']
        ];

        foreach($values as $type => $value) {
            $this->assertReturnsNativeType(function () { $var; }, $type, ['var' => $this->typeSystem->getTypeFromValue($value)]);
        }
    }

    public function testResourceWithVariable()
    {
        $this->assertReturnsNativeType(
                function () { $var; },
                INativeType::TYPE_RESOURCE,
                ['var' => $this->typeSystem->getTypeFromValue(fopen('php://memory', 'r'))]);
    }

    public function testInvalidVariable()
    {
        $this->assertAnalysisFails(function ($foo) { $bar; });
    }

    public function testCasts()
    {
        $values = [
                INativeType::TYPE_STRING  => 'abc',
                INativeType::TYPE_INT     => -34,
                INativeType::TYPE_BOOL => true,
                INativeType::TYPE_DOUBLE  => -4.2454,
                INativeType::TYPE_NULL    => null,
                INativeType::TYPE_ARRAY   => [222, '']
        ];

        foreach($values as $value) {
            $variableType = ['var' => $this->typeSystem->getTypeFromValue($value)];
            if(!is_array($value)) {
                $this->assertReturnsNativeType(function () { (string)$var; }, INativeType::TYPE_STRING, $variableType);
            }
            $this->assertReturnsNativeType(function () { (int)$var; }, INativeType::TYPE_INT, $variableType);
            $this->assertReturnsNativeType(function () { (bool)$var; }, INativeType::TYPE_BOOL, $variableType);
            $this->assertReturnsNativeType(function () { (double)$var; }, INativeType::TYPE_DOUBLE, $variableType);
            $this->assertReturnsNativeType(function () { (array)$var; }, INativeType::TYPE_ARRAY, $variableType);
            $this->assertReturnsObjectType(function () { (object)$var; }, 'stdClass', $variableType);
        }
    }

    public function testInvalidCasts()
    {
        $this->assertAnalysisFails(function () { (string)['abc']; });
    }

    public function testUnaryOperators()
    {
        $asserts = [
                [function () { +1; }, INativeType::TYPE_INT],
                [function () { -1; }, INativeType::TYPE_INT],
                [function () { ~1; }, INativeType::TYPE_INT],
                [function () { ++$i; }, INativeType::TYPE_INT, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                [function () { --$i; }, INativeType::TYPE_INT, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                [function () { $i++; }, INativeType::TYPE_INT, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                [function () { $i--; }, INativeType::TYPE_INT, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                [function () { +''; }, INativeType::TYPE_INT],
                [function () { -''; }, INativeType::TYPE_INT],
                [function () { ~''; }, INativeType::TYPE_STRING],
                [function () { ++$i; }, INativeType::TYPE_MIXED, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_STRING)]],
                [function () { --$i; }, INativeType::TYPE_MIXED, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_STRING)]],
                [function () { $i++; }, INativeType::TYPE_STRING, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_STRING)]],
                [function () { $i--; }, INativeType::TYPE_STRING, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_STRING)]],
        ];

        foreach($asserts as $assert) {
            $this->assertReturnsNativeType($assert[0], $assert[1], isset($assert[2]) ? $assert[2] : []);
        }
    }

    public function testInvalidUnaryOperators()
    {
        $asserts = [
                [function ($a) { +[$a]; }],
                [function ($a) { -[$a]; }],
                [function ($a) { ~[$a]; }],
                [function () { ++$i; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY)]],
                [function () { --$i; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY)]],
                [function () { $i++; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY)]],
                [function () { $i--; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY)]],
        ];

        foreach($asserts as $assert) {
            $this->assertAnalysisFails($assert[0], isset($assert[1]) ? $assert[1] : []);
        }
    }

    public function testFunctionCalls()
    {
        $this->assertReturnsNativeType(function () { strlen(''); }, INativeType::TYPE_INT);
        $this->assertReturnsNativeType(function () { is_string(''); }, INativeType::TYPE_BOOL);
    }

    public function testInvalidFunctionCall()
    {
        $this->assertAnalysisFails(function () { qwertyuiop(); });
        $this->assertAnalysisFails(function ($var) { $var(); });
    }

    public function testStaticMethodCall()
    {
        $this->assertReturnsObjectType(function () { \DateTime::createFromFormat(); }, 'DateTime');
        $this->assertReturnsNativeType(function () { \DateTime::getLastErrors(); }, INativeType::TYPE_ARRAY);
    }

    public function testStaticMethodCallOnInstance()
    {
        $this->assertReturnsObjectType(function (\DateTime $instance) { $instance->createFromFormat(); }, 'DateTime');
    }

    public function testInvalidStaticMethodCall()
    {
        $this->assertAnalysisFails(function () { \DateTime::AasfFFD(); });
        $this->assertAnalysisFails(function ($var) { \DateTime::$var(); });
    }

    public function testStaticField()
    {
        $this->assertReturnsNativeType(function () { self::$field; }, INativeType::TYPE_MIXED);
    }

    public function testInvalidStaticField()
    {
        $this->assertAnalysisFails(function () { \DateTimeZone::$abcdef; });
        $this->assertAnalysisFails(function ($var) { \DateTime::$$var; });
        //PHP does not allow instance access of static fields unlike methods
        $this->assertAnalysisFails(function (self $instance) { $instance->field; });
    }

    public function testNew()
    {
        $this->assertReturnsObjectType(function () { new \stdClass; }, 'stdClass');
        $this->assertReturnsObjectType(function () { new \DateTime(); }, 'DateTime');
    }

    public function testInvalidNew()
    {
        $this->assertAnalysisFails(function () { new sdsdsdvds(); });
        $this->assertAnalysisFails(function ($var) { new $var(); });
    }

    public function testMethodCalls()
    {
        $this->assertReturnsNativeType(function (\DateTime $dateTime) { $dateTime->format(''); }, INativeType::TYPE_STRING);
        $this->assertReturnsNativeType(function (ITraversable $traversable) { $traversable->count(); }, INativeType::TYPE_INT);
    }

    public function testInvocation()
    {
        $this->assertReturnsNativeType(function (\Closure $closure) { $closure(); }, INativeType::TYPE_MIXED);
    }

    public function testInvalidInvocation()
    {
        $this->assertAnalysisFails(function (\stdClass $class) { $class(); });
    }

    public function testInvalidMethodCall()
    {
        $this->assertAnalysisFails(function (\DateTime $invalid) { $invalid->abcd(); });
        $this->assertAnalysisFails(function (\DateTime $invalid, $var) { $invalid->$var(); });
    }

    public function testFields()
    {
        $this->assertReturnsNativeType(function (\DateInterval $dateInterval) { $dateInterval->y; }, INativeType::TYPE_INT);
        $this->assertReturnsNativeType(function (\DateInterval $dateInterval) { $dateInterval->m; }, INativeType::TYPE_INT);
        $this->assertReturnsNativeType(function (\DateInterval $dateInterval) { $dateInterval->d; }, INativeType::TYPE_INT);
        $this->assertReturnsNativeType(function (\DateInterval $dateInterval) { $dateInterval->days; }, INativeType::TYPE_MIXED);
    }

    public function testInvalidField()
    {
        $this->assertAnalysisFails(function (\DateTime $invalid) { $invalid->foo; });
        $this->assertAnalysisFails(function (\DateTime $invalid, $var) { $invalid->$var; });
    }

    public function testIndexers()
    {
        $this->assertReturnsNativeType(function (array $array) { $array['foo']; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function (\ArrayAccess $arrayAccess) { $arrayAccess[3]; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function (\ArrayAccess $arrayAccess) { $arrayAccess['var']; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function (ITraversable $traversable) { $traversable['bar']; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function (IQueryable $traversable) { $traversable['bar']; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function (IRepository $traversable) { $traversable['bar']; }, INativeType::TYPE_MIXED);
    }

    public function testInvalidIndexers()
    {
        $this->assertAnalysisFails(function (\DateTime $invalid) { $invalid['123a']; });
    }

    public function testIsset()
    {
        $this->assertReturnsNativeType(function ($foo) { isset($foo); }, INativeType::TYPE_BOOL);
        $this->assertReturnsNativeType(function ($foo, \DateInterval $bar) { isset($foo, $bar->y); }, INativeType::TYPE_BOOL);
        $this->assertReturnsNativeType(function (\ArrayAccess $foo) { isset($foo['abc']); }, INativeType::TYPE_BOOL);
    }

    public function testEmpty()
    {
        $this->assertReturnsNativeType(function ($foo) { empty($foo); }, INativeType::TYPE_BOOL);
        $this->assertReturnsNativeType(function (\DateInterval $foo) { empty($foo->m); }, INativeType::TYPE_BOOL);
        $this->assertReturnsNativeType(function (array $foo) { empty($foo['abc']); }, INativeType::TYPE_BOOL);
    }

    public function testClosure()
    {
        $this->assertReturnsObjectType(function () { function ($i) {}; }, 'Closure');
        $this->assertReturnsObjectType(function () { function ($i) { return 3454; }; }, 'Closure');
        $this->assertReturnsObjectType(function (\Closure $var) { $var->bindTo(__CLASS__)->bindTo(__CLASS__)->bindTo(__CLASS__)->bindTo(__CLASS__); }, 'Closure');
    }

    public function testBinaryOperators()
    {
        $asserts = [
                INativeType::TYPE_INT => [
                        function () { 1 + 1; },
                        function () { 1 - 1; },
                        function () { 1 * 1; },
                        function () { 1 & 1; },
                        function () { '' & 1; },
                        function () { 1 | 1; },
                        function () { '' | 1; },
                        function () { 1 << 1; },
                        function () { 1.0 << 1; },
                        function () { 1 >> 1; },
                        function () { 1 >> 1.0; },
                        function () { 1 ^ 1; },
                        function () { 1 ^ 1.0; },
                        function () { 1.0 ^ 1.0; },
                ],
                INativeType::TYPE_DOUBLE => [
                        function () { 1 + 1.0; },
                        function () { 1.0 + 1; },
                        function () { 1.0 + 1.0; },
                        function () { 1 - 1.0; },
                        function () { 1.0 - 1; },
                        function () { 1.0 - 1.0; },
                        function () { 1 * 1.0; },
                        function () { 1.0 * 1; },
                        function () { 1.0 * 1.0; },
                        function () { 3.4 / 24; },
                        function () { 34 / 2.4; },
                        function () { 3.4 / 2.34; },
                ],
                INativeType::TYPE_BOOL => [
                        function () { 1 && 1.0; },
                        function () { 1 && 0; },
                        function () { true && 0; },
                        function () { 0 && true; },
                        function () { false && true; },
                        function () { '' && true; },
                        function () { false && ''; },
                        function () { 2.3 && true; },
                        function () { false && 2.1; },
                        function () { [] && true; },
                        function () { false && [1,2]; },
                        function () { 1 || 1.0; },
                        function () { 1 || 0; },
                        function () { true || 0; },
                        function () { 0 || true; },
                        function () { false || true; },
                        function () { '' || true; },
                        function () { false || ''; },
                        function () { 2.3 || true; },
                        function () { false || 2.1; },
                        function () { [] || true; },
                        function () { false || [1,2]; },
                        function () { 3 < 3; },
                        function () { 3 < 3.0; },
                        function () { 3 < '3'; },
                        function () { 3 <= 3; },
                        function () { 3 <= '3'; },
                        function () { 3.0 <= 3; },
                        function () { 3 > 3; },
                        function () { 3.0 > 3; },
                        function () { 3 > '3'; },
                        function () { 3 > 3.0; },
                        function () { 3.0 >= 3; },
                        function () { 3 >= 3.0; },
                        function () { 3 >= '3'; },
                        function ($a) { (false || $a) instanceof \stdClass; },
                ],
                INativeType::TYPE_ARRAY => [
                        function () { [] + [1,2]; },
                        function () { [] + [1,2,3] + [2] + ['abc']; },
                ],
                INativeType::TYPE_STRING => [
                        function () { 'abc' . '123'; },
                        function () { 'abc' . 123; },
                        function () { 'abc' . 123.42; },
                        function () { 123 . 'ab'; },
                        function () { 123.42 . 'a'; },
                        function () { 2 . 3.45; },
                        function () { false . ''; },
                        function () { '' . true; },
                        function () { false . true; },
                        function () { false . 3.2; },
                        function () { 3 . 9; },
                ],
                INativeType::TYPE_NUMERIC => [
                        function () { '123' + 1; },
                        function () { '123' - 1; },
                        function () { '123' * 1; },
                        function () { '123' + 1; },
                        function () { 3 + 'av1'; },
                        function () { 3 - 'av1'; },
                        function () { 3 * 'av1'; },
                        function ($a) { 3 / ('av1' . $a); },
                        function () { 'as' + 'av1'; },
                        function () { 1 / 2; },
                        function () { 1 / 1; },
                        function () { '123' / 24; },
                ],
        ];

        foreach($asserts as $expectedType => $expressions)
        {
            foreach($expressions as $expression) {
                $this->assertReturnsNativeType($expression, $expectedType);
            }
        }
    }

    public function testAssignmentOperators()
    {
        $asserts = [
                INativeType::TYPE_INT => [
                        [function () { $var = 1; }],
                        [function () { $i %= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i ^= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i &= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i |= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i >>= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i <<= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i += 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                        [function () { $i -= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_INT)]],
                ],
                INativeType::TYPE_DOUBLE => [
                        [function ($var) { $i = 3.22; }],
                        [function () { $i += 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_DOUBLE)]],
                        [function () { $i -= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_DOUBLE)]],
                ],
                INativeType::TYPE_BOOL => [
                        [function ($var) { $i = true; }],
                ],
                INativeType::TYPE_ARRAY => [
                        [function () { $i = [1,12]; }],
                        [function () { $i += [1,12]; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY)]],
                ],
                INativeType::TYPE_STRING => [
                        [function ($var) { $var .= 1; }],
                ],
                INativeType::TYPE_NUMERIC => [
                        [function () { $i += 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_STRING)]],
                        [function () { $i -= 1; }, ['i' => $this->typeSystem->getNativeType(INativeType::TYPE_STRING)]],
                ],
                INativeType::TYPE_MIXED => [
                        [function ($var) { $i = $var; }],
                        [function ($var) { $i =& $var; }],
                ],
        ];

        foreach($asserts as $expectedType => $expressions)
        {
            foreach($expressions as $assert) {
                $this->assertReturnsNativeType($assert[0], $expectedType, isset($assert[1]) ? $assert[1] : []);
            }
        }
    }

    public function testInvalidBinaryOperator()
    {
        $this->assertAnalysisFails(function ($a) { [$a] - 3.4; });
    }

    public function testTernaryWithNativeTypes()
    {
        $asserts = [
                INativeType::TYPE_INT => [
                        function () { true ? 1 : 2; },
                        function () { true ? 31 : -2; },
                        function () { true ? strlen('') : 2; },
                ],
                INativeType::TYPE_DOUBLE => [
                        function () { true ? 1.0 : 2.0; },
                        function () { true ? 1.23 : 2.34; },
                ],
                INativeType::TYPE_BOOL => [
                        function () { true ? true : false; },
                        function () { true ? true : (bool)0; },
                        function () { true ? : (bool)0; },
                ],
                INativeType::TYPE_ARRAY => [
                        function () { true ? [] : []; },
                        function () { true ? [] : [2434]; },
                        function () { true ? [1,2, []] : ([4] + []); },
                ],
                INativeType::TYPE_STRING => [
                        function () { true ? '22' : ''; },
                        function () { true ? 'abc' : '343'; },
                        function () { true ? (string)2 : '343'; },
                ],
                INativeType::TYPE_NUMERIC => [
                        function () { true ? 1 : 2.0; },
                ],
                INativeType::TYPE_MIXED => [
                        function () { true ? strlen('') : 'abc'; },
                        function () { true ? [] : 123; },
                        function () { true ? [] : new \stdClass(); },
                        function () { true ? 2434 : new \stdClass(); },
                        function () { true ? new \DateTime() : new \stdClass(); },
                        function () { 'abc' ? : new \stdClass(); },
                ],
        ];

        foreach($asserts as $expectedType => $expressions)
        {
            foreach($expressions as $expression) {
                $this->assertReturnsNativeType($expression, $expectedType);
            }
        }

        $this->assertReturnsNativeType($expression, $expectedType);
    }

    public function testTernaryWithObjectTypes()
    {
        $asserts = [
                'stdClass' => [
                        function () { true ? new \stdClass() : new \stdClass(); },
                        function () { true ? new \stdClass() : (object)[]; },
                        function () { true ? (object)[1,2,4] : (object)[]; },
                ],
                'Traversable' => [
                        function (\Iterator $a, \IteratorAggregate $b) { true ? $a : $b; },
                ],
                'ArrayObject' => [
                        function (\ArrayObject $a, \ArrayObject $b) { true ? $a : $b; },
                ],
        ];

        foreach($asserts as $expectedType => $expressions)
        {
            foreach($expressions as $expression) {
                $this->assertReturnsObjectType($expression, $expectedType);
            }
        }

        $this->assertReturnsType(
                function (\ArrayObject $a, \ArrayIterator $b) {
                    0 ? $a : $b;
                },
                $this->typeSystem->getCompositeType(
                        [
                                $this->typeSystem->getObjectType('Countable'),
                                $this->typeSystem->getObjectType('ArrayAccess'),
                                $this->typeSystem->getObjectType('Traversable'),
                                $this->typeSystem->getObjectType('Serializable'),
                        ]
                )
        );
    }

    public function testVariableTableFromEvaluationContextFromScopedVariables()
    {
        foreach ([1, null, true, 3.4, 'abc', new DateTime()] as $value) {
            $this->assertReturnsType(
                    function () use ($value) { $value; },
                    $this->typeSystem->getTypeFromValue($value)
            );
        }
    }

    public function testConstantTypeAnalysis()
    {
        $this->assertReturnsNativeType(
                function () { SORT_ASC; },
                INativeType::TYPE_INT
        );

        $this->assertReturnsNativeType(
                function () { M_PI; },
                INativeType::TYPE_DOUBLE
        );
    }

    public function testClassConstantTypeAnalysis()
    {
        $this->assertReturnsNativeType(
                function () { \ArrayObject::ARRAY_AS_PROPS; },
                INativeType::TYPE_INT
        );

        $this->assertReturnsNativeType(
                function () { \DateTime::ATOM; },
                INativeType::TYPE_STRING
        );
    }

    public function testInvalidClassConstantTypeAnalysis()
    {
        $this->assertAnalysisFails(function ($foo) { $foo::ARRAY_AS_PROPS; });
    }
}