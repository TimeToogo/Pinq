<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

//Used to test relative namespaced function calls
function get_called_class()
{
    return 'global override';
}

class ExpressionSimplificationTest extends InterpreterTest
{
    public static $field;
    private static $privateField;
    const FOO_BAR_CONST = 'ttddbb11';
    
    //Backup globals is not working for this class...
    private static $globalsBackup = [];
    
    public static function setUpBeforeClass()
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
    
    public static function tearDownAfterClass()
    {
        list($_SERVER, $_ENV, $_REQUEST, $_GET, $_POST, $_COOKIE, $_FILES) = self::$globalsBackup;
    }
    
    protected function setUp()
    {
        self::$field = null;
        self::$privateField = null;
        //Testing session superglobal (not defined without session_start())
        $_SESSION = [];
    }
    
    private function assertSimplifiesTo(callable $function, $value, $useNativeEquals = false, $namespace = __NAMESPACE__)
    {
        $interpreter = $this->verifyImplementation();
        $reflection = $interpreter->getReflection($function);
        $structure = $interpreter->getStructure($reflection);
        
        $bodyExpressions = $structure->getBodyExpressions();
        $this->assertCount(1, $bodyExpressions);
        
        if($value instanceof O\Expression) {
            $simplifiedValue = $bodyExpressions[0]->simplify($reflection->getScope(), $namespace);
        } else {
            $simplifiedValue = $bodyExpressions[0]->simplifyToValue($reflection->getScope(), $namespace);
        }
        
        if($useNativeEquals) {
            $this->assertTrue($value == $simplifiedValue);
        } else {
            $this->assertEquals($value, $simplifiedValue);
        }
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testBinaryOperators()
    {
        $this->assertSimplifiesTo(function () { 2 + 1; },        3);
        $this->assertSimplifiesTo(function () { 5 - 1; },        4);
        $this->assertSimplifiesTo(function () { 5 * 2; },        10);
        $this->assertSimplifiesTo(function () { 20 / 4; },       5);
        $this->assertSimplifiesTo(function () { 15 % 4; },       3);
        $this->assertSimplifiesTo(function () { 100 & 20; },     4);
        $this->assertSimplifiesTo(function () { 70 | 10; },      78);
        $this->assertSimplifiesTo(function () { 2 << 5; },       64);
        $this->assertSimplifiesTo(function () { 128 >> 2; },     32);
        $this->assertSimplifiesTo(function () { true && -1; },   true);
        $this->assertSimplifiesTo(function () { false || 0; },   false);
        $this->assertSimplifiesTo(function () { 1 === 1; },      true);
        $this->assertSimplifiesTo(function () { 0 !== 1; },      true);
        $this->assertSimplifiesTo(function () { '1' == 1; },     true);
        $this->assertSimplifiesTo(function () { 34 != 34; },     false);
        $this->assertSimplifiesTo(function () { 5 > 1; },        true);
        $this->assertSimplifiesTo(function () { 2.2 >= 2; },     true);
        $this->assertSimplifiesTo(function () { 1 < 1; },        false);
        $this->assertSimplifiesTo(function () { 1.1 <= 1; },     false);
        $this->assertSimplifiesTo(function () { 'foo' . 1; },    'foo1');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableBinaryOperators()
    {
        $this->assertSimplifiesTo(function () { $var + 1; }, 
                O\Expression::binaryOperation(
                        O\Expression::variable(O\Expression::value('var')),
                        O\Operators\Binary::ADDITION, 
                        O\Expression::value(1)));
    }

    /**
     * @dataProvider interpreters
     */
    public function testUnaryOperation()
    {
        $this->assertSimplifiesTo(function () { -5; },       -5);
        $this->assertSimplifiesTo(function () { !true; },    false);
        $this->assertSimplifiesTo(function () { ~2; },       -3);
        $this->assertSimplifiesTo(function () { +44; },      44);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableUnaryOperator()
    {
        $this->assertSimplifiesTo(function () { !$var; }, 
                O\Expression::unaryOperation(
                        O\Operators\Unary::NOT,
                        O\Expression::variable(O\Expression::value('var'))));
    }

    /**
     * @dataProvider interpreters
     */
    public function testCastOperations()
    {
        $this->assertSimplifiesTo(function () { (string)4; },    '4');
        $this->assertSimplifiesTo(function () { (bool)0; },      false);
        $this->assertSimplifiesTo(function () { (int)6.9; },     6);
        $this->assertSimplifiesTo(function () { (array)0; },     [0 => 0]);
        $this->assertSimplifiesTo(function () { (object)null; }, new \stdClass());
        $this->assertSimplifiesTo(function () { (double)'1'; },  1.0);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableCastOperator()
    {
        $this->assertSimplifiesTo(function () { (bool)$var; }, 
                O\Expression::cast(
                        O\Operators\Cast::BOOLEAN,
                        O\Expression::variable(O\Expression::value('var'))));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testArrayDeclaration()
    {
        $this->assertSimplifiesTo(function () { [
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
    public function testNonSimplifyableArrayDeclaration()
    {
        $this->assertSimplifiesTo(function () { ['foo' => 1.2, $bar => 'ttt']; }, 
                O\Expression::arrayExpression([
                    O\Expression::arrayItem(O\Expression::value('foo'), O\Expression::value(1.2), false),
                    O\Expression::arrayItem(O\Expression::variable(O\Expression::value('bar')), O\Expression::value('ttt'), false),
                ]));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testTernary()
    {
        $this->assertSimplifiesTo(function () { 1 ? 'left' : 'right'; },        'left');
        $this->assertSimplifiesTo(function () { 0 ? 'left' : 'right'; },        'right');
        $this->assertSimplifiesTo(function () { true ? 'left' : 'right'; },     'left');
        $this->assertSimplifiesTo(function () { false ? 'left' : 'right'; },    'right');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableTernary()
    {
        $this->assertSimplifiesTo(function () { $var ? 'left' : 'right'; }, 
                O\Expression::ternary(
                        O\Expression::variable(O\Expression::value('var')),
                        O\Expression::value('left'),
                        O\Expression::value('right')));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testFunctionCalls()
    {
        $this->assertSimplifiesTo(function () { get_class(); }, __CLASS__);
        $this->assertSimplifiesTo(function () { abs(-123); }, 123);
        $this->assertSimplifiesTo(function () { sqrt(625); }, 25);
        $this->assertSimplifiesTo(function () { log(9, 3); }, 2);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testFunctionCallsNamespaceResolution()
    {
        //Use relative function defined at the top of this file
        $this->assertSimplifiesTo(function () { get_called_class(); }, 'global override');
        //Test falls back to non global function
        $this->assertSimplifiesTo(function () { get_called_class(); }, \get_called_class(), false, __NAMESPACE__ . '__');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableFunctionCalls()
    {
        $this->assertSimplifiesTo(function () { sqrt($var); }, 
                O\Expression::functionCall(
                        O\Expression::value('sqrt'), 
                        [O\Expression::variable(O\Expression::value('var'))]));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testStaticField()
    {
        self::$privateField = 'I AM PRIVATE';
        self::$field = [1, 2, 3];
        
        $this->assertSimplifiesTo(function () { ExpressionSimplificationTest::$field; }, [1,2,3]);
        //Ensure resolves scope
        $this->assertSimplifiesTo(function () { self::$field; }, [1,2,3]);
        //Ensure scope allows correct access
        $this->assertSimplifiesTo(function () { self::$privateField; }, 'I AM PRIVATE');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableStaticField()
    {
        $this->assertSimplifiesTo(function () { $var::$field; }, 
                O\Expression::staticField(
                        O\Expression::variable(O\Expression::value('var')), 
                        O\Expression::value('field')));
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
        $this->assertSimplifiesTo(function () { ExpressionSimplificationTest::method(); }, 'STATIC METHOD');
        $this->assertSimplifiesTo(function () { self::method(); }, 'STATIC METHOD');
        $this->assertSimplifiesTo(function () { self::privateMethod(); }, 'PRIVATE STATIC METHOD');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableStaticMethod()
    {
        $this->assertSimplifiesTo(function () { $var::method(); }, 
                O\Expression::staticMethodCall(
                        O\Expression::variable(O\Expression::value('var')), 
                        O\Expression::value('method')));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testFields()
    {
        self::$field = (object)['foobar' => 123];
        self::$privateField = (object)['private' => 'posh'];
        
        $this->assertSimplifiesTo(function () { self::$field->foobar; }, 123);
        $this->assertSimplifiesTo(function () { self::$privateField->private; }, 'posh');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableFields()
    {
        $this->assertSimplifiesTo(function () { $var->field; }, 
                O\Expression::field(
                        O\Expression::variable(O\Expression::value('var')), 
                        O\Expression::value('field')));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testMethods()
    {
        self::$field = (new \DateTime())->setTimestamp(1001);
        self::$privateField = new \ArrayObject(range(1, 10));
        
        $this->assertSimplifiesTo(function () { self::$field->getTimestamp(); }, 1001);
        $this->assertSimplifiesTo(function () { self::$privateField->getArrayCopy(); }, range(1, 10));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableMethods()
    {
        $this->assertSimplifiesTo(function () { $var->method(); }, 
                O\Expression::methodCall(
                        O\Expression::variable(O\Expression::value('var')), 
                        O\Expression::value('method')));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testIndexers()
    {
        self::$field = new \ArrayObject(['foo' => 'bar-qux']);
        self::$privateField = new \ArrayIterator([4 => [1,23]]);
        
        $this->assertSimplifiesTo(function () { self::$field['foo']; }, 'bar-qux');
        $this->assertSimplifiesTo(function () { self::$privateField[4]; }, [1,23]);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableIndexers()
    {
        $this->assertSimplifiesTo(function () { $var['index']; }, 
                O\Expression::index(
                        O\Expression::variable(O\Expression::value('var')), 
                        O\Expression::value('index')));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNew()
    {
        $this->assertSimplifiesTo(function () { new \stdClass(); }, new \stdClass());
        $this->assertSimplifiesTo(function () { new \ArrayObject(); }, new \ArrayObject());
        $this->assertSimplifiesTo(function () { new \ArrayObject(range(1, 20)); }, new \ArrayObject(range(1, 20)));
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableNew()
    {
        $this->assertSimplifiesTo(function () { new $var(); }, 
                O\Expression::newExpression(
                        O\Expression::variable(O\Expression::value('var'))));
    }
    
    /**
     * @dataProvider interpreters
     * @link http://www.abacustraining.biz/bodmasExercises.htm
     */
    public function testComplexMath()
    {
        $this->assertSimplifiesTo(function () { ((90+4)/2-6*7/2+((7+4)*10)+8*9-5)*10+8; }, 2038);
        $this->assertSimplifiesTo(function () { ((3+4-6/2+2)+((9/3+6*5)/11))*((4+5-6)+(18-3*4))/9; }, 9);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testComplexTraversal()
    {
        $this->assertSimplifiesTo(function () { count((new \ArrayObject(range(1, 19)))->getIterator()->getArrayCopy()); }, 19);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testConstants()
    {
        $this->assertSimplifiesTo(function () { SORT_ASC; }, SORT_ASC);
        $this->assertSimplifiesTo(function () { SORT_DESC; }, SORT_DESC);
    }
    
    
    /**
     * @dataProvider interpreters
     */
    public function testConstantsNamespaceResolution()
    {
        if(!defined(__NAMESPACE__ . '\\CONST_FOO_BAR')) {
            define(__NAMESPACE__ . '\\CONST_FOO_BAR', 'abcdefghijklmnop');
        }
        if(!defined('CONST_FOO_BAR')) {
            define('CONST_FOO_BAR', 'global-abcdefghijklmnop');
        }
        
        $this->assertSimplifiesTo(function () { CONST_FOO_BAR; }, 'abcdefghijklmnop');
        //Fallback to global if not found in relative namespace
        $this->assertSimplifiesTo(function () { CONST_FOO_BAR; }, 'global-abcdefghijklmnop', false, __NAMESPACE__ . '__');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testClassConstants()
    {
        $this->assertSimplifiesTo(function () { \ArrayObject::ARRAY_AS_PROPS; }, \ArrayObject::ARRAY_AS_PROPS);
        $this->assertSimplifiesTo(function () { \ArrayObject::STD_PROP_LIST; }, \ArrayObject::STD_PROP_LIST);
        $this->assertSimplifiesTo(function () { ExpressionSimplificationTest::FOO_BAR_CONST; }, self::FOO_BAR_CONST);
        $this->assertSimplifiesTo(function () { self::FOO_BAR_CONST; }, self::FOO_BAR_CONST);
        $this->assertSimplifiesTo(function () { static::FOO_BAR_CONST; }, static::FOO_BAR_CONST);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableClassConstants()
    {
        $this->assertSimplifiesTo(function () { $var::CONSTANT; }, 
                O\Expression::classConstant(
                        O\Expression::variable(O\Expression::value('var')),
                        'CONSTANT'));
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testSuperGlobals()
    {
        $this->assertSimplifiesTo(function () { $_COOKIE; }, $_COOKIE);
        $this->assertSimplifiesTo(function () { $_ENV; }, $_ENV);
        $this->assertSimplifiesTo(function () { $_FILES; }, $_FILES);
        $this->assertSimplifiesTo(function () { $_GET; }, $_GET);
        $this->assertSimplifiesTo(function () { $_POST; }, $_POST);
        $this->assertSimplifiesTo(function () { $_REQUEST; }, $_REQUEST);
        $this->assertSimplifiesTo(function () { $_SERVER; }, $_SERVER);
        $this->assertSimplifiesTo(function () { $_SESSION; }, $_SESSION);
        
        $_COOKIE['foo'] = 'cookie';
        $this->assertSimplifiesTo(function () { $_COOKIE['foo']; }, 'cookie');
        $_ENV['boo'] = 'environment';
        $this->assertSimplifiesTo(function () { $_ENV['boo']; }, 'environment');
        $_FILES['fff'] = 'files';
        $this->assertSimplifiesTo(function () { $_FILES['fff']; }, 'files');
        $_GET['stuff'] = 'get';
        $this->assertSimplifiesTo(function () { $_GET['stuff']; }, 'get');
        $_POST['other'] = 'post';
        $this->assertSimplifiesTo(function () { $_POST['other']; }, 'post');
        $_REQUEST['bar'] = 'request';
        $this->assertSimplifiesTo(function () { $_REQUEST['bar']; }, 'request');
        $_SERVER['opts'] = 'server';
        $this->assertSimplifiesTo(function () { $_SERVER['opts']; }, 'server');
        $_SESSION['var'] = 'session';
        $this->assertSimplifiesTo(function () { $_SESSION['var']; }, 'session');
        
        $_COOKIE = [1, 'ttt'];
        $this->assertSimplifiesTo(function () { $_COOKIE; },  [1, 'ttt']);
        $_ENV = '12345';
        $this->assertSimplifiesTo(function () { $_ENV; }, '12345');
        $_FILES = 93.4;
        $this->assertSimplifiesTo(function () { $_FILES; }, 93.4);
        $_GET = range(1, 10, 2);
        $this->assertSimplifiesTo(function () { $_GET; }, range(1, 10, 2));
        $_POST = 'a post array';
        $this->assertSimplifiesTo(function () { $_POST; }, 'a post array');
        $_REQUEST = ['fooo'];
        $this->assertSimplifiesTo(function () { $_REQUEST; }, ['fooo']);
        //$_SERVER must be array or PHPUnit goes boom.
        $_SERVER = ['p' => false];
        $this->assertSimplifiesTo(function () { $_SERVER; }, ['p' => false]);
        $_SESSION = [9,7,5,4,'111'];
        $this->assertSimplifiesTo(function () { $_SESSION; }, [9,7,5,4,'111']);
        
        $var = [1];
        $_COOKIE =& $var;
        $this->assertSimplifiesTo(function () { $_COOKIE; }, $var);
        $_ENV =& $var;
        $this->assertSimplifiesTo(function () { $_ENV; }, $var);
        $_FILES =& $var;
        $this->assertSimplifiesTo(function () { $_FILES; }, $var);
        $_GET =& $var;
        $this->assertSimplifiesTo(function () { $_GET; }, $var);
        $_POST =& $var;
        $this->assertSimplifiesTo(function () { $_POST; }, $var);
        $_REQUEST =& $var;
        $this->assertSimplifiesTo(function () { $_REQUEST; }, $var);
        $_SERVER =& $var;
        $this->assertSimplifiesTo(function () { $_SERVER; }, $var);
        $_SESSION =& $var;
        $this->assertSimplifiesTo(function () { $_SESSION; }, $var);
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testGlobal()
    {
        //PHPUnit cant handle assertEquals with this?
        $this->assertSimplifiesTo(function () { $GLOBALS; }, $GLOBALS, true);
        
        $GLOBALS['poopop'] = 'globals';
        $this->assertSimplifiesTo(function () { $GLOBALS['poopop']; }, 'globals');
        
        $GLOBALS = ['abcdef'];
        $this->assertSimplifiesTo(function () { $GLOBALS; }, ['abcdef']);
        
        $var = [1];
        $GLOBALS =& $var;
        $this->assertSimplifiesTo(function () { $GLOBALS; }, $var);
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testIsset()
    {
        $this->assertSimplifiesTo(function () { isset($GLOBALS['a1232323']); }, false);
        
        $GLOBALS['a1232323'] = null;
        $this->assertSimplifiesTo(function () { isset($GLOBALS['a1232323']); }, false);
        
        $GLOBALS['a1232323'] = 'val';
        $this->assertSimplifiesTo(function () { isset($GLOBALS['a1232323']); }, true);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableIsset()
    {
        $this->assertSimplifiesTo(function () { isset($var); }, 
                O\Expression::issetExpression([
                    O\Expression::variable(O\Expression::value('var'))]));
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testIssetWithFields()
    {
        $this->assertSimplifiesTo(function () { isset($GLOBALS['bddbbddb']->foo); }, false);
        
        $GLOBALS['bddbbddb'] = true;
        $this->assertSimplifiesTo(function () { isset($GLOBALS['bddbbddb']->foo); }, false);
        
        $GLOBALS['bddbbddb'] = (object)['foo' => true];
        $this->assertSimplifiesTo(function () { isset($GLOBALS['bddbbddb']->foo); }, true);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testIssetWithStaticFields()
    {
        $this->assertSimplifiesTo(function () { isset(self::$nonExistantField); }, false);
        
        self::$field = null;
        $this->assertSimplifiesTo(function () { isset(self::$field); }, false);
        
        self::$field = 1;
        $this->assertSimplifiesTo(function () { isset(self::$field); }, true);
        
        $this->assertSimplifiesTo(function () { isset(self::$field['foo']); }, false);
        
        self::$field = ['foo' => true];
        $this->assertSimplifiesTo(function () { isset(self::$field['foo']); }, true);
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     * @expectedException PHPUnit_Framework_Error_Notice
     * @expectedExceptionMessage Trying to get property of non-object
     */
    public function testIssetWithFieldsInIndexEmitsNotice()
    {
        $this->assertSimplifiesTo(function () { isset($GLOBALS[$GLOBALS->foo]); }, null);
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testEmpty()
    {
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, true);
        
        $GLOBALS['b3213232'] = true;
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, false);
        
        $GLOBALS['b3213232'] = null;
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, true);
        
        $GLOBALS['b3213232'] = 0;
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, true);
        
        $GLOBALS['b3213232'] = [];
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, true);
        
        $GLOBALS['b3213232'] = '';
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, true);
        
        $GLOBALS['b3213232'] = [1,2,3];
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, false);
        
        $GLOBALS['b3213232'] = new \stdClass();
        $this->assertSimplifiesTo(function () { empty($GLOBALS['b3213232']); }, false);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableEmpty()
    {
        $this->assertSimplifiesTo(function () { empty($var); }, 
                O\Expression::emptyExpression(
                    O\Expression::variable(O\Expression::value('var'))));
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testEmptyWithFields()
    {
        $this->assertSimplifiesTo(function () { empty($GLOBALS['dfdfdfda']->foo); }, true);
        
        $GLOBALS['dfdfdfda'] = 'bar';
        $this->assertSimplifiesTo(function () { empty($GLOBALS['dfdfdfda']->foo); }, true);
        
        $GLOBALS['dfdfdfda'] = (object)['foo' => true];
        $this->assertSimplifiesTo(function () { empty($GLOBALS['dfdfdfda']->foo); }, false);
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testEmptyWithStaticFields()
    {
        self::$field = false;
        $this->assertSimplifiesTo(function () { empty(self::$field); }, true);
        
        self::$field = 1;
        $this->assertSimplifiesTo(function () { empty(self::$field); }, false);
        
        $this->assertSimplifiesTo(function () { empty(self::$field['foo']); }, true);
        
        self::$field = ['foo' => true];
        $this->assertSimplifiesTo(function () { empty(self::$field['foo']); }, false);
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     * @expectedException PHPUnit_Framework_Error_Notice
     * @expectedExceptionMessage Trying to get property of non-object
     */
    public function testEmptyWithFieldsInIndexEmitsNotice()
    {
        $this->assertSimplifiesTo(function () { empty($GLOBALS[$GLOBALS->foo]); }, null);
    }
    
    /**
     * @dataProvider interpreters
     * @backupGlobals enabled
     */
    public function testInvocation()
    {
        $GLOBALS['__testInvocable'] = function () { return 'foo-bar-quz'; };
        
        $this->assertSimplifiesTo(function () { $GLOBALS['__testInvocable'](); }, 'foo-bar-quz');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testNonSimplifyableInvocation()
    {
        $this->assertSimplifiesTo(function () { $var(); }, 
                O\Expression::invocation(
                        O\Expression::variable(O\Expression::value('var'))));
    }
}
