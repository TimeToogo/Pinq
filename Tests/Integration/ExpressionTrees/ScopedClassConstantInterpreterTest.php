<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

class ScopedClassConstantInterpreterTest extends ScopedClassInterpreterBaseTest
{
    /**
     * @var ScopedClassConstants
     */
    protected $constants;

    protected function setUp()
    {
        if(version_compare(PHP_VERSION, '5.5.0', '>=')) {
            require_once __DIR__ . '/ScopedClassConstants.php';
            $this->constants = new ScopedClassConstants();
        } else {
            $this->markTestSkipped('Requires >=PHP 5.5');
        }
    }


    /**
     * @dataProvider interpreters
     */
    public function testConcrete()
    {
        $this->assertRecompilesWithRebind($this->constants->concreteClass());
    }


    /**
     * @dataProvider interpreters
     */
    public function testSelf()
    {
        $this->assertRecompilesWithRebind($this->constants->selfClass());
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testStatic()
    {
        $this->assertRecompilesWithRebind($this->constants->staticClass());
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testParent()
    {
        $this->assertRecompilesWithRebind($this->constants->parentClass());
    }
    
    /**
     * @dataProvider interpreters
     */
    public function testParsedFunctionWithScopedClassConstantsInParameters()
    {
        $this->assertRecompilesWithRebind($this->constants->selfParameter());
        $this->assertRecompilesWithRebind($this->constants->selfParameterComplex());
    }
}