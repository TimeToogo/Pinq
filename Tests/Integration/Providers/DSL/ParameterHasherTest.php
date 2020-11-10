<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ParameterHasher;
use Pinq\Queries;
use Pinq\Queries\Functions;
use Pinq\Queryable;
use Pinq\Tests\PinqTestCase;
use Pinq\Traversable;
use Pinq\PinqException;

function userDefinedFunction(array &$gg = [1, 2, 3], $t = __LINE__, \stdClass $f = null)
{

}

class ParameterHasherTest extends PinqTestCase
{
    public function testValueTypeHasher()
    {
        $hasher = ParameterHasher::valueType();

        foreach ([1, 'erfse', 'DF$T$TWG$', 34343.34, null, true, false] as $value) {
            $this->assertSame($hasher->hash($value), $hasher->hash($value));
        }

        $this->assertNotSame($hasher->hash(3), $hasher->hash(5));
        $this->assertNotSame($hasher->hash('3'), $hasher->hash(3));
        $this->assertNotSame($hasher->hash(null), $hasher->hash(0));
        $this->assertNotSame($hasher->hash(null), $hasher->hash(false));
        $this->assertNotSame($hasher->hash(true), $hasher->hash(false));
        $this->assertNotSame($hasher->hash('abcdefg1'), $hasher->hash('abcdefg2'));
    }

    public static function staticFunction($t)
    {
        return $t;
    }

    public function testFunctionSignatureHasher()
    {
        $hasher = ParameterHasher::functionSignature();

        foreach ([
                         'strlen',
                         function () { },
                         [$this, 'getName'],
                         [$this, 'testFunctionSignatureHasher'],
                         __NAMESPACE__ . '\\userDefinedFunction',
                         [__CLASS__, 'staticFunction']
                 ] as $function) {
            $this->assertSame($hasher->hash($function), $hasher->hash($function));
        }

        //Indistinguishable signatures:
        $this->assertSame($hasher->hash(function () { }), $hasher->hash(function () { }));
        $this->assertSame($hasher->hash(function (\stdClass $foo = null) { }), $hasher->hash(function (\stdClass $foo = null) { }));
        //Case insensitive functions:
        $this->assertSame($hasher->hash('StrLen'), $hasher->hash('strleN'));

        $this->assertNotSame($hasher->hash('strlen'), $hasher->hash('strpos'));
        $this->assertNotSame($hasher->hash('stripos'), $hasher->hash('strpos'));
        $this->assertNotSame($hasher->hash([__CLASS__, 'staticFunction']), $hasher->hash(__NAMESPACE__ . '\\userDefinedFunction'));
        $this->assertNotSame($hasher->hash([__CLASS__, 'staticFunction']), $hasher->hash(__NAMESPACE__ . '\\userDefinedFunction'));
        $this->assertNotSame($hasher->hash(function ($i) { }), $hasher->hash(function ($o) { }));
        $this->assertNotSame($hasher->hash(function ($i) { }), $hasher->hash(function & ($i) { }));
        $this->assertNotSame($hasher->hash(function ($i) { }), $hasher->hash(function (&$i) { }));
        $this->assertNotSame($hasher->hash(function ($i) { }), $hasher->hash(function ($i, $j) { }));
        $this->assertNotSame($hasher->hash(function ($i) { }), $hasher->hash(function ($i = null) { }));
        //Same signature but distinguishable location
        $this->assertNotSame(
                $hasher->hash(function ($i) { }),
                $hasher->hash(function ($i) { }));
    }

    public function testCompiledRequestQueryHasherThrowsExceptionForNonQueryable()
    {
        $this->expectException(PinqException::class);
        $hasher = ParameterHasher::compiledRequestQuery();

        $hasher->hash(new \stdClass());
    }

    public function testCompiledRequestQueryHasherThrowsExceptionForQueryableWithoutDSLProvider()
    {
        $this->expectException(PinqException::class);
        $hasher = ParameterHasher::compiledRequestQuery();

        $hasher->hash((new \Pinq\Providers\Traversable\Provider(Traversable::from([])))->createQueryable());
    }

    public function testCompiledRequestQueryHasher()
    {
        $hasher = ParameterHasher::compiledRequestQuery();

        $configurationMock = $this->getMockBuilder('Pinq\\Providers\\DSL\\QueryCompilerConfiguration')
                ->setMethods(['getCompiledRequestQueryHash'])
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
        $configurationMock->expects($this->once())
                ->method('getCompiledRequestQueryHash')
                ->will($this->returnValue('123456789'));

        /** @var $provider \Pinq\Providers\DSL\QueryProvider|\PHPUnit_Framework_MockObject_MockObject */
        $provider = $this->getMockForAbstractClass(
                'Pinq\\Providers\\DSL\\QueryProvider',
                [new Queries\SourceInfo(''), $configurationMock]
        );

        $queryable = $provider->createQueryable();

        $this->assertSame($hasher->hash($queryable), '123456789');
    }
}
 