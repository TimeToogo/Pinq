<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

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

        $evaluationContext = $reflection->getScope()->asEvaluationContext([], $namespace);
        if($value instanceof O\Expression) {
            $simplifiedValue = $bodyExpressions[0]->simplify($evaluationContext);
        } else {
            $simplifiedValue = $bodyExpressions[0]->simplifyToValue($evaluationContext);
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
    public function testUsedVariableBinaryOperators()
    {
        $var = -1;
        $this->assertSimplifiesTo(function () use ($var) { $var + 1; }, 0);
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
    public function testUsedVariableUnaryOperator()
    {
        $var = true;
        $this->assertSimplifiesTo(function () use ($var) { !$var; }, false);
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
    public function testUsedVariableCastOperator()
    {
        $var = 'hi';
        $this->assertSimplifiesTo(function () use ($var) { (bool)$var; }, true);
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
    public function testUsedVariableArrayDeclaration()
    {
        $var = 'foo';
        $this->assertSimplifiesTo(
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
        $this->assertSimplifiesTo(function () { 1 ? 'left' : 'right'; },        'left');
        $this->assertSimplifiesTo(function () { 0 ? 'left' : 'right'; },        'right');
        $this->assertSimplifiesTo(function () { true ? 'left' : 'right'; },     'left');
        $this->assertSimplifiesTo(function () { false ? 'left' : 'right'; },    'right');
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableTernary()
    {
        $var = 'foo';
        $this->assertSimplifiesTo(function () use ($var) { $var ? 'left' : 'right'; }, 'left');
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
        $var = 25;
        $this->assertSimplifiesTo(function () use ($var) { sqrt($var); }, 5);
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
    public function testUsedVariableStaticField()
    {
        self::$field = [1, 23, 3];
        $var = __CLASS__;
        $this->assertSimplifiesTo(function () use ($var) { $var::$field; }, [1, 23, 3]);
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
    public function testUsedVariableStaticMethod()
    {
        $var = __CLASS__;
        $this->assertSimplifiesTo(function () use ($var) { $var::method(); }, 'STATIC METHOD');
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

    protected $instanceField = 'foo-bar';
    
    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableFields()
    {
        $var = $this;
        $this->assertSimplifiesTo(function () use ($var) { $var->instanceField; }, 'foo-bar');
        $this->assertSimplifiesTo(function () { $this->instanceField; }, 'foo-bar');
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
    public function testUsedVariableMethods()
    {
        $var = $this;
        $this->assertSimplifiesTo(function () use ($var) { $var->method(); }, 'STATIC METHOD');
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
    public function testUsedVariableIndexers()
    {
        $var = ['index' => 'foo'];
        $this->assertSimplifiesTo(function () use ($var) { $var['index']; }, 'foo');
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
    public function testUsedVariableNew()
    {
        $var = 'stdClass';
        $this->assertSimplifiesTo(function () use ($var) { new $var(); }, new \stdClass());
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
    public function testUsedVariableClassConstants()
    {
        $var = __CLASS__;
        $this->assertSimplifiesTo(function () use ($var) { $var::FOO_BAR_CONST; }, self::FOO_BAR_CONST);
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
    public function testUsedVariableIsset()
    {

        $this->assertSimplifiesTo(function () { isset($var); }, false);
        $var = null;
        $this->assertSimplifiesTo(function () use ($var) { isset($var); }, false);
        $var = 1;
        $this->assertSimplifiesTo(function () use ($var) { isset($var); }, true);
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
    public function testUsedVariableEmpty()
    {
        $this->assertSimplifiesTo(function () { empty($var); }, true);
        $var = [1];
        $this->assertSimplifiesTo(function () use ($var) { empty($var); }, false);
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
        $GLOBALS['__testInvokable'] = function () { return 'foo-bar-quz'; };
        
        $this->assertSimplifiesTo(function () { $GLOBALS['__testInvokable'](); }, 'foo-bar-quz');
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableInvocation()
    {
        $var = function () { return 'value'; };
        $this->assertSimplifiesTo(function () use ($var) { $var(); }, 'value');
    }

    /**
     * @dataProvider interpreters
     */
    public function testInvokableObjectWorksWithScope()
    {
        $this->assertSimplifiesTo(new ScopedStaticVariableInvokableObject(), true);
        $this->assertSimplifiesTo(new ExtendedScopedMethodCallInvokableObject(), true);
        $this->assertSimplifiesTo(new ScopedMethodCallInvokableObject(), true);
        $this->assertSimplifiesTo(new ExtendedScopedMethodCallInvokableObject(), true);
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
        $this->assertSimplifiesTo([$this, 'methodWithScopedStaticVariable'], true);
        $this->assertSimplifiesTo([$this, 'methodWithScopedStaticMethodCall'], true);
    }

    /**
     * @dataProvider interpreters
     */
    public function testClosureUsedVariableReference()
    {
        $ref = true;
        $this->assertSimplifiesTo(function () use (&$ref) { $ref = false; }, false);
        $this->assertFalse($ref);
    }
}
