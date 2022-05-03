<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use PHPUnit\Framework\Error\Notice;
use Pinq\Expressions as O;

//Used to test relative namespaced function calls
function get_called_class()
{
    return 'global override';
}

class ScopedStaticVariableInvokableObject
{
    private static $scopedField = true;

    public function __invoke()
    {
        self::$scopedField;
    }
}

class ExtendedScopedStaticVariableInvokableObject extends ScopedStaticVariableInvokableObject
{

}

class ScopedMethodCallInvokableObject
{
    public function __invoke()
    {
        $this->privateMethod();
    }

    private function privateMethod()
    {
        return true;
    }
}

class ExtendedScopedMethodCallInvokableObject extends ScopedMethodCallInvokableObject
{

}

class ExpressionEvaluationTest extends InterpreterTest
{
    public static $field;
    private static $privateField;
    const FOO_BAR_CONST = 'ttddbb11';

    //Backup globals is not working for this class...
    private static $globalsBackup = [];

    public static function setUpBeforeClass(): void
    {
        self::$globalsBackup = [
            $_SERVER,
            $_ENV,
            $_REQUEST,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,];
    }

    public static function tearDownAfterClass(): void
    {
        list($_SERVER, $_ENV, $_REQUEST, $_GET, $_POST, $_COOKIE, $_FILES) = self::$globalsBackup;
    }

    protected function setUp(): void
    {
        self::$field = null;
        self::$privateField = null;
        //Testing session superglobal (not defined without session_start())
        $_SESSION = [];
    }

    private function assertEvaluatesTo(callable $function, $value, $useNativeEquals = false, $namespace = __NAMESPACE__, $testSerialize = false)
    {
        $interpreter = $this->verifyImplementation();
        $reflection = $interpreter->getReflection($function);
        $structure = $interpreter->getStructure($reflection);

        $bodyExpressions = $structure->getBodyExpressions();
        $this->assertCount(1, $bodyExpressions);

        $scope             = $reflection->getScope();
        $evaluationContext = $scope->asEvaluationContext([], $namespace);
        $evaluator         = $bodyExpressions[0]->asEvaluator($evaluationContext);
        $evaluatedValues   = [];
        $evaluatedValues[] = $evaluator->evaluate();
        if ($testSerialize) {
            $evaluatedValues[] = unserialize(serialize($evaluator))->evaluate();
        }

        foreach ($evaluatedValues as $evaluatedValue) {
            if ($useNativeEquals) {
                $this->assertTrue($value == $evaluatedValue);
            } else {
                $this->assertEquals($value, $evaluatedValue);
            }
        }
    }

    private function assertSerializedEvaluatesTo(callable $function, $value, $useNativeEquals = false, $namespace = __NAMESPACE__)
    {
        $this->assertEvaluatesTo($function, $value, $useNativeEquals, $namespace, true);
    }

    /**
     * @dataProvider interpreters
     */
    public function testBinaryOperators()
    {
        $this->assertEvaluatesTo(function () { 2 + 1; },        3);
        $this->assertEvaluatesTo(function () { 5 - 1; },        4);
        $this->assertEvaluatesTo(function () { 5 * 2; },        10);
        $this->assertEvaluatesTo(function () { 20 / 4; },       5);
        $this->assertEvaluatesTo(function () { 15 % 4; },       3);
        $this->assertEvaluatesTo(function () { 100 & 20; },     4);
        $this->assertEvaluatesTo(function () { 70 | 10; },      78);
        $this->assertEvaluatesTo(function () { 2 << 5; },       64);
        $this->assertEvaluatesTo(function () { 128 >> 2; },     32);
        $this->assertEvaluatesTo(function () { true && -1; },   true);
        $this->assertEvaluatesTo(function () { false || 0; },   false);
        $this->assertEvaluatesTo(function () { 1 === 1; },      true);
        $this->assertEvaluatesTo(function () { 0 !== 1; },      true);
        $this->assertEvaluatesTo(function () { '1' == 1; },     true);
        $this->assertEvaluatesTo(function () { 34 != 34; },     false);
        $this->assertEvaluatesTo(function () { 5 > 1; },        true);
        $this->assertEvaluatesTo(function () { 2.2 >= 2; },     true);
        $this->assertEvaluatesTo(function () { 1 < 1; },        false);
        $this->assertEvaluatesTo(function () { 1.1 <= 1; },     false);
        $this->assertEvaluatesTo(function () { 'foo' . 1; },    'foo1');
    }

    /**
     * @dataProvider interpreters
     */
    public function testSerializeValue()
    {
        $var = '1234';
        $this->assertSerializedEvaluatesTo(static function () { 942.676; }, 942.676);
        $this->assertSerializedEvaluatesTo(static function () { [1,2,3,3443, __NAMESPACE__, '3334', [[]]]; }, [1,2,3,3443, __NAMESPACE__, '3334', [[]]]);
    }

    /**
     * @dataProvider interpreters
     */
    public function testBinaryOperatorsSerialize()
    {
        $this->assertSerializedEvaluatesTo(static function () { 2 + 1; }, 3);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableBinaryOperators()
    {
        $var = -1;
        $this->assertEvaluatesTo(function () use ($var) { $var + 1; }, 0);
    }

    /**
     * @dataProvider interpreters
     */
    public function testSerializeUsedVariable()
    {
        $var = '1234';
        $this->assertSerializedEvaluatesTo(static function () use ($var) { $var . '--'; }, '1234--');
        $this->assertSerializedEvaluatesTo(static function () use ($var) { $var; }, '1234');
    }

    /**
     * @dataProvider interpreters
     */
    public function testUnaryOperation()
    {
        $this->assertEvaluatesTo(function () { -5; },       -5);
        $this->assertEvaluatesTo(function () { !true; },    false);
        $this->assertEvaluatesTo(function () { ~2; },       -3);
        $this->assertEvaluatesTo(function () { +44; },      44);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableUnaryOperator()
    {
        $var = true;
        $this->assertEvaluatesTo(function () use ($var) { !$var; }, false);
    }

    /**
     * @dataProvider interpreters
     */
    public function testCastOperations()
    {
        $this->assertEvaluatesTo(function () { (string) 4; },    '4');
        $this->assertEvaluatesTo(function () { (bool) 0; },      false);
        $this->assertEvaluatesTo(function () { (int) 6.9; },     6);
        $this->assertEvaluatesTo(function () { (array) 0; },     [0 => 0]);
        $this->assertEvaluatesTo(function () { (object) null; }, new \stdClass());
        $this->assertEvaluatesTo(function () { (double) '1'; },  1.0);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableCastOperator()
    {
        $var = 'hi';
        $this->assertEvaluatesTo(function () use ($var) { (bool) $var; }, true);
    }

    /**
     * @dataProvider interpreters
     */
    public function testArrayDeclaration()
    {
        $this->assertEvaluatesTo(function () { [
            1,
            null,
            'test',
            false,
            new \stdClass(),
            [1234567890, 'tests', new \stdClass()]
        ]; },
        [
            1,
            null,
            'test',
            false,
            new \stdClass(),
            [1234567890, 'tests', new \stdClass()]
        ]);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableArrayDeclaration()
    {
        $var = 'foo';
        $this->assertEvaluatesTo(
                function () use ($var) {
                    ['foo' => 1.2, $var => 'ttt'];
                },
                ['foo' => 1.2, $var => 'ttt']
        );
    }

    /**
     * @dataProvider interpreters
     */
    public function testTernary()
    {
        $this->assertEvaluatesTo(function () { 1 ? 'left' : 'right'; },        'left');
        $this->assertEvaluatesTo(function () { 0 ? 'left' : 'right'; },        'right');
        $this->assertEvaluatesTo(function () { true ? 'left' : 'right'; },     'left');
        $this->assertEvaluatesTo(function () { false ? 'left' : 'right'; },    'right');
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableTernary()
    {
        $var = 'foo';
        $this->assertEvaluatesTo(function () use ($var) { $var ? 'left' : 'right'; }, 'left');
    }

    /**
     * @dataProvider interpreters
     */
    public function testFunctionCalls()
    {
        $this->assertEvaluatesTo(function () { get_class(); }, __CLASS__);
        $this->assertEvaluatesTo(function () { abs(-123); }, 123);
        $this->assertEvaluatesTo(function () { sqrt(625); }, 25);
        $this->assertEvaluatesTo(function () { log(9, 3); }, 2);
    }

    /**
     * @dataProvider interpreters
     */
    public function testFunctionCallsNamespaceResolution()
    {
        //Use relative function defined at the top of this file
        $this->assertSerializedEvaluatesTo(static function () { get_called_class(); }, 'global override');
        //Test falls back to non global function
        $this->assertSerializedEvaluatesTo(static function () { get_called_class(); }, \get_called_class(), false, __NAMESPACE__ . '__');
    }

    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableFunctionCalls()
    {
        $var = 25;
        $this->assertEvaluatesTo(function () use ($var) { sqrt($var); }, 5);
    }

    /**
     * @dataProvider interpreters
     */
    public function testStaticField()
    {
        self::$privateField = 'I AM PRIVATE';
        self::$field = [1, 2, 3];

        $this->assertSerializedEvaluatesTo(static function () { ExpressionEvaluationTest::$field; }, [1,2,3]);
        //Ensure resolves scope
        $this->assertSerializedEvaluatesTo(static function () { self::$field; }, [1,2,3]);
        //Ensure scope allows correct access
        $this->assertSerializedEvaluatesTo(static function () { self::$privateField; }, 'I AM PRIVATE');
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableStaticField()
    {
        self::$field = [1, 23, 3];
        $var = __CLASS__;
        $this->assertEvaluatesTo(function () use ($var) { $var::$field; }, [1, 23, 3]);
    }

    public static function method()
    {
        return 'STATIC METHOD';
    }

    private static function privateMethod()
    {
        return 'PRIVATE STATIC METHOD';
    }

    /**
     * @dataProvider interpreters
     */
    public function testStaticMethod()
    {
        $this->assertEvaluatesTo(function () { ExpressionEvaluationTest::method(); }, 'STATIC METHOD');
        $this->assertEvaluatesTo(function () { self::method(); }, 'STATIC METHOD');
        $this->assertEvaluatesTo(function () { self::privateMethod(); }, 'PRIVATE STATIC METHOD');
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableStaticMethod()
    {
        $var = __CLASS__;
        $this->assertEvaluatesTo(function () use ($var) { $var::method(); }, 'STATIC METHOD');
    }

    /**
     * @dataProvider interpreters
     */
    public function testFields()
    {
        self::$field = (object) ['foobar' => 123];
        self::$privateField = (object) ['private' => 'posh'];

        $this->assertEvaluatesTo(function () { self::$field->foobar; }, 123);
        $this->assertEvaluatesTo(function () { self::$privateField->private; }, 'posh');
    }

    protected $instanceField = 'foo-bar';

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableFields()
    {
        $var = $this;
        $this->assertEvaluatesTo(function () use ($var) { $var->instanceField; }, 'foo-bar');
        $this->assertEvaluatesTo(function () { $this->instanceField; }, 'foo-bar');
    }

    /**
     * @dataProvider interpreters
     */
    public function testMethods()
    {
        self::$field = (new \DateTime())->setTimestamp(1001);
        self::$privateField = new \ArrayObject(range(1, 10));

        $this->assertEvaluatesTo(function () { self::$field->getTimestamp(); }, 1001);
        $this->assertEvaluatesTo(function () { self::$privateField->getArrayCopy(); }, range(1, 10));
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableMethods()
    {
        $var = $this;
        $this->assertEvaluatesTo(function () use ($var) { $var->method(); }, 'STATIC METHOD');
    }

    /**
     * @dataProvider interpreters
     */
    public function testIndexers()
    {
        self::$field = new \ArrayObject(['foo' => 'bar-qux']);
        self::$privateField = new \ArrayIterator([4 => [1,23]]);

        $this->assertEvaluatesTo(function () { self::$field['foo']; }, 'bar-qux');
        $this->assertEvaluatesTo(function () { self::$privateField[4]; }, [1,23]);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableIndexers()
    {
        $var = ['index' => 'foo'];
        $this->assertEvaluatesTo(function () use ($var) { $var['index']; }, 'foo');
    }

    /**
     * @dataProvider interpreters
     */
    public function testNew()
    {
        $this->assertEvaluatesTo(function () { new \stdClass(); }, new \stdClass());
        $this->assertEvaluatesTo(function () { new \ArrayObject(); }, new \ArrayObject());
        $this->assertEvaluatesTo(function () { new \ArrayObject(range(1, 20)); }, new \ArrayObject(range(1, 20)));
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableNew()
    {
        $var = 'stdClass';
        $this->assertEvaluatesTo(function () use ($var) { new $var(); }, new \stdClass());
    }

    /**
     * @dataProvider interpreters
     * @link http://www.abacustraining.biz/bodmasExercises.htm
     */
    public function testComplexMath()
    {
        $this->assertEvaluatesTo(function () { ((90+4)/2-6*7/2+((7+4)*10)+8*9-5)*10+8; }, 2038);
        $this->assertEvaluatesTo(function () { ((3+4-6/2+2)+((9/3+6*5)/11))*((4+5-6)+(18-3*4))/9; }, 9);
    }

    /**
     * @dataProvider interpreters
     */
    public function testComplexTraversal()
    {
        $this->assertEvaluatesTo(function () { count((new \ArrayObject(range(1, 19)))->getIterator()->getArrayCopy()); }, 19);
    }

    /**
     * @dataProvider interpreters
     */
    public function testConstants()
    {
        $this->assertEvaluatesTo(function () { SORT_ASC; }, SORT_ASC);
        $this->assertEvaluatesTo(function () { SORT_DESC; }, SORT_DESC);
    }

    /**
     * @dataProvider interpreters
     */
    public function testConstantsNamespaceResolution()
    {
        if (!defined(__NAMESPACE__ . '\\CONST_FOO_BAR')) {
            define(__NAMESPACE__ . '\\CONST_FOO_BAR', 'abcdefghijklmnop');
        }
        if (!defined('CONST_FOO_BAR')) {
            define('CONST_FOO_BAR', 'global-abcdefghijklmnop');
        }

        $this->assertEvaluatesTo(function () { CONST_FOO_BAR; }, 'abcdefghijklmnop');
        //Fallback to global if not found in relative namespace
        $this->assertEvaluatesTo(function () { CONST_FOO_BAR; }, 'global-abcdefghijklmnop', false, __NAMESPACE__ . '__');
    }

    /**
     * @dataProvider interpreters
     */
    public function testClassConstants()
    {
        $this->assertEvaluatesTo(function () { \ArrayObject::ARRAY_AS_PROPS; }, \ArrayObject::ARRAY_AS_PROPS);
        $this->assertEvaluatesTo(function () { \ArrayObject::STD_PROP_LIST; }, \ArrayObject::STD_PROP_LIST);
        $this->assertEvaluatesTo(function () { ExpressionEvaluationTest::FOO_BAR_CONST; }, self::FOO_BAR_CONST);
        $this->assertEvaluatesTo(function () { self::FOO_BAR_CONST; }, self::FOO_BAR_CONST);
        $this->assertEvaluatesTo(function () { static::FOO_BAR_CONST; }, static::FOO_BAR_CONST);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableClassConstants()
    {
        $var = __CLASS__;
        $this->assertEvaluatesTo(function () use ($var) { $var::FOO_BAR_CONST; }, self::FOO_BAR_CONST);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testSuperGlobals()
    {
        $this->assertEvaluatesTo(function () { $_COOKIE; }, $_COOKIE);
        $this->assertEvaluatesTo(function () { $_ENV; }, $_ENV);
        $this->assertEvaluatesTo(function () { $_FILES; }, $_FILES);
        $this->assertEvaluatesTo(function () { $_GET; }, $_GET);
        $this->assertEvaluatesTo(function () { $_POST; }, $_POST);
        $this->assertEvaluatesTo(function () { $_REQUEST; }, $_REQUEST);
        $this->assertEvaluatesTo(function () { $_SERVER; }, $_SERVER);
        $this->assertEvaluatesTo(function () { $_SESSION; }, $_SESSION);

        $_COOKIE['foo'] = 'cookie';
        $this->assertEvaluatesTo(function () { $_COOKIE['foo']; }, 'cookie');
        $_ENV['boo'] = 'environment';
        $this->assertEvaluatesTo(function () { $_ENV['boo']; }, 'environment');
        $_FILES['fff'] = 'files';
        $this->assertEvaluatesTo(function () { $_FILES['fff']; }, 'files');
        $_GET['stuff'] = 'get';
        $this->assertEvaluatesTo(function () { $_GET['stuff']; }, 'get');
        $_POST['other'] = 'post';
        $this->assertEvaluatesTo(function () { $_POST['other']; }, 'post');
        $_REQUEST['bar'] = 'request';
        $this->assertEvaluatesTo(function () { $_REQUEST['bar']; }, 'request');
        $_SERVER['opts'] = 'server';
        $this->assertEvaluatesTo(function () { $_SERVER['opts']; }, 'server');
        $_SESSION['var'] = 'session';
        $this->assertEvaluatesTo(function () { $_SESSION['var']; }, 'session');

        $_COOKIE = [1, 'ttt'];
        $this->assertEvaluatesTo(function () { $_COOKIE; },  [1, 'ttt']);
        $_ENV = '12345';
        $this->assertEvaluatesTo(function () { $_ENV; }, '12345');
        $_FILES = 93.4;
        $this->assertEvaluatesTo(function () { $_FILES; }, 93.4);
        $_GET = range(1, 10, 2);
        $this->assertEvaluatesTo(function () { $_GET; }, range(1, 10, 2));
        $_POST = 'a post array';
        $this->assertEvaluatesTo(function () { $_POST; }, 'a post array');
        $_REQUEST = ['fooo'];
        $this->assertEvaluatesTo(function () { $_REQUEST; }, ['fooo']);
        //$_SERVER must be array or PHPUnit goes boom.
        $_SERVER = ['p' => false];
        $this->assertEvaluatesTo(function () { $_SERVER; }, ['p' => false]);
        $_SESSION = [9,7,5,4,'111'];
        $this->assertEvaluatesTo(function () { $_SESSION; }, [9,7,5,4,'111']);

        $var = [1];
        $_COOKIE =& $var;
        $this->assertEvaluatesTo(function () { $_COOKIE; }, $var);
        $_ENV =& $var;
        $this->assertEvaluatesTo(function () { $_ENV; }, $var);
        $_FILES =& $var;
        $this->assertEvaluatesTo(function () { $_FILES; }, $var);
        $_GET =& $var;
        $this->assertEvaluatesTo(function () { $_GET; }, $var);
        $_POST =& $var;
        $this->assertEvaluatesTo(function () { $_POST; }, $var);
        $_REQUEST =& $var;
        $this->assertEvaluatesTo(function () { $_REQUEST; }, $var);
        $_SERVER =& $var;
        $this->assertEvaluatesTo(function () { $_SERVER; }, $var);
        $_SESSION =& $var;
        $this->assertEvaluatesTo(function () { $_SESSION; }, $var);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testGlobal()
    {
        //PHPUnit cant handle assertEquals with this?
        $this->assertEvaluatesTo(function () { $GLOBALS; }, $GLOBALS, true);

        $GLOBALS['poopop'] = 'globals';
        $this->assertEvaluatesTo(function () { $GLOBALS['poopop']; }, 'globals');
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testIsset()
    {
        $this->assertEvaluatesTo(function () { isset($GLOBALS['a1232323']); }, false);

        $GLOBALS['a1232323'] = null;
        $this->assertEvaluatesTo(function () { isset($GLOBALS['a1232323']); }, false);

        $GLOBALS['a1232323'] = 'val';
        $this->assertEvaluatesTo(function () { isset($GLOBALS['a1232323']); }, true);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableIsset()
    {

        $this->assertEvaluatesTo(function () { isset($var); }, false);
        $var = null;
        $this->assertEvaluatesTo(function () use ($var) { isset($var); }, false);
        $var = 1;
        $this->assertEvaluatesTo(function () use ($var) { isset($var); }, true);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testIssetWithFields()
    {
        $this->assertEvaluatesTo(function () { isset($GLOBALS['bddbbddb']->foo); }, false);

        $GLOBALS['bddbbddb'] = true;
        $this->assertEvaluatesTo(function () { isset($GLOBALS['bddbbddb']->foo); }, false);

        $GLOBALS['bddbbddb'] = (object) ['foo' => true];
        $this->assertEvaluatesTo(function () { isset($GLOBALS['bddbbddb']->foo); }, true);
    }

    /**
     * @dataProvider interpreters
     */
    public function testIssetWithStaticFields()
    {
        $this->assertEvaluatesTo(function () { isset(self::$nonExistantField); }, false);

        self::$field = null;
        $this->assertEvaluatesTo(function () { isset(self::$field); }, false);

        self::$field = 1;
        $this->assertEvaluatesTo(function () { isset(self::$field); }, true);

        $this->assertEvaluatesTo(function () { isset(self::$field['foo']); }, false);

        self::$field = ['foo' => true];
        $this->assertEvaluatesTo(function () { isset(self::$field['foo']); }, true);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testIssetWithFieldsInIndexEmitsNotice()
    {
        PHP_VERSION_ID >= 80000 ? $this->expectWarning() : $this->expectNotice();
        PHP_VERSION_ID >= 80000 ? $this->expectExceptionMessage('Attempt to read property "foo" on array') : $this->expectExceptionMessage("Trying to get property 'foo' of non-object");
        $this->assertEvaluatesTo(function () { isset($GLOBALS[$GLOBALS->foo]); }, null);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testEmpty()
    {
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, true);

        $GLOBALS['b3213232'] = true;
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, false);

        $GLOBALS['b3213232'] = null;
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, true);

        $GLOBALS['b3213232'] = 0;
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, true);

        $GLOBALS['b3213232'] = [];
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, true);

        $GLOBALS['b3213232'] = '';
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, true);

        $GLOBALS['b3213232'] = [1,2,3];
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, false);

        $GLOBALS['b3213232'] = new \stdClass();
        $this->assertEvaluatesTo(function () { empty($GLOBALS['b3213232']); }, false);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableEmpty()
    {
        $this->assertEvaluatesTo(function () { empty($var); }, true);
        $var = [1];
        $this->assertEvaluatesTo(function () use ($var) { empty($var); }, false);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testEmptyWithFields()
    {
        $this->assertEvaluatesTo(function () { empty($GLOBALS['dfdfdfda']->foo); }, true);

        $GLOBALS['dfdfdfda'] = 'bar';
        $this->assertEvaluatesTo(function () { empty($GLOBALS['dfdfdfda']->foo); }, true);

        $GLOBALS['dfdfdfda'] = (object) ['foo' => true];
        $this->assertEvaluatesTo(function () { empty($GLOBALS['dfdfdfda']->foo); }, false);
    }

    /**
     * @dataProvider interpreters
     */
    public function testEmptyWithStaticFields()
    {
        self::$field = false;
        $this->assertEvaluatesTo(function () { empty(self::$field); }, true);

        self::$field = 1;
        $this->assertEvaluatesTo(function () { empty(self::$field); }, false);

        $this->assertEvaluatesTo(function () { empty(self::$field['foo']); }, true);

        self::$field = ['foo' => true];
        $this->assertEvaluatesTo(function () { empty(self::$field['foo']); }, false);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testEmptyWithFieldsInIndexEmitsNotice()
    {
        PHP_VERSION_ID >= 80000 ? $this->expectWarning() : $this->expectNotice();
        PHP_VERSION_ID >= 80000 ?
            $this->expectExceptionMessage('Attempt to read property "foo" on array')
            : $this->expectExceptionMessage("Trying to get property 'foo' of non-object");

        $this->assertEvaluatesTo(function () { empty($GLOBALS[$GLOBALS->foo]); }, null);
    }

    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testInvocation()
    {
        $GLOBALS['__testInvokable'] = function () { return 'foo-bar-quz'; };

        $this->assertEvaluatesTo(function () { $GLOBALS['__testInvokable'](); }, 'foo-bar-quz');
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableInvocation()
    {
        $var = function () { return 'value'; };
        $this->assertEvaluatesTo(function () use ($var) { $var(); }, 'value');
    }

    /**
     * @dataProvider interpreters
     */
    public function testInvokableObjectWorksWithScope()
    {
        $this->assertEvaluatesTo(new ScopedStaticVariableInvokableObject(), true);
        $this->assertEvaluatesTo(new ExtendedScopedMethodCallInvokableObject(), true);
        $this->assertEvaluatesTo(new ScopedMethodCallInvokableObject(), true);
        $this->assertEvaluatesTo(new ExtendedScopedMethodCallInvokableObject(), true);
    }

    private static $scopeForMethod = true;

    /**
     * @dataProvider interpreters
     */
    public function methodWithScopedStaticVariable()
    {
        self::$scopeForMethod;
    }

    /**
     * @dataProvider interpreters
     */
    public function methodWithScopedStaticMethodCall()
    {
        $this->privateMethodCall();
    }

    /**
     * @dataProvider interpreters
     */
    public function privateMethodCall()
    {
        return true;
    }

    /**
     * @dataProvider interpreters
     */
    public function testMethodWorksWithScope()
    {
        $this->assertEvaluatesTo([$this, 'methodWithScopedStaticVariable'], true);
        $this->assertEvaluatesTo([$this, 'methodWithScopedStaticMethodCall'], true);
    }

    /**
     * @dataProvider interpreters
     */
    public function testClosureUsedVariableReference()
    {
        $ref = true;
        $this->assertEvaluatesTo(function () use (&$ref) { $ref = false; }, false);
        $this->assertFalse($ref);
    }

    public function testWithSpecialVariableNames()
    {
        $this->assertSame(
                '1,2,3',
                O\CompiledEvaluator::fromExpressions(
                        [O\Expression::returnExpression(
                                O\Expression::variable(O\Expression::value('a special var--'))
                        )],
                        O\EvaluationContext::globalScope(null, ['a special var--' => '1,2,3'])
                )->evaluate()
        );
    }

    /**
     * @dataProvider interpreters
     */
    public function testBlankIndex()
    {
        $ref = [];
        $this->assertEvaluatesTo(function () use (&$ref) { $ref[] = 76; }, 76);
        $this->assertEquals([76], $ref);
        $ref = [];
        $this->assertEvaluatesTo(function () use (&$ref) { $ref[][][] = 75; }, 75);
        $this->assertEquals([[[75]]], $ref);
    }
}
