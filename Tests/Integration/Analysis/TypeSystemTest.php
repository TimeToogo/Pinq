<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\IIndexer;
use Pinq\Analysis\INativeType;
use Pinq\Analysis\IType;
use Pinq\Analysis\PhpTypeSystem;
use Pinq\Analysis\TypeData\TypeDataModule;
use Pinq\Analysis\TypeException;
use Pinq\Analysis\TypeId;
use Pinq\Expressions as O;
use Pinq\ICollection;
use Pinq\IRepository;
use Pinq\ITraversable;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TypeSystemTest extends ExpressionAnalysisTestCase
{
    public function testTypeValueResolution()
    {
        $values = [
                INativeType::TYPE_STRING   => 'abc',
                INativeType::TYPE_INT      => -34,
                INativeType::TYPE_BOOL     => true,
                INativeType::TYPE_DOUBLE   => -4.2454,
                INativeType::TYPE_NULL     => null,
                INativeType::TYPE_ARRAY    => [222, ''],
                INativeType::TYPE_RESOURCE => fopen('php://memory', 'r')
        ];

        foreach ($values as $expectedType =>  $value) {
            $this->assertEqualsNativeType($expectedType, $this->typeSystem->getTypeFromValue($value));
        }

        $this->assertEqualTypes($this->typeSystem->getObjectType('stdClass'), $this->typeSystem->getTypeFromValue(new \stdClass()));
    }

    public function testTypeHintTypeResolution()
    {
        $this->assertEqualsNativeType(INativeType::TYPE_ARRAY, $this->typeSystem->getTypeFromTypeHint('array'));
        $this->assertEqualsNativeType(INativeType::TYPE_ARRAY, $this->typeSystem->getTypeFromTypeHint('aRRay'));
        $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $this->typeSystem->getTypeFromTypeHint('callable'));
        $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $this->typeSystem->getTypeFromTypeHint('cAllABle'));
        $this->assertEqualsObjectType('\\stdClass', $this->typeSystem->getTypeFromTypeHint('\\stdClass'));
        $this->assertEqualsObjectType('\\stdClass', $this->typeSystem->getTypeFromTypeHint('\\stdCLASS'));
        $this->assertEqualsObjectType('\\stdClass', $this->typeSystem->getTypeFromTypeHint('\\STDclASS'));
        $this->assertEqualsObjectType('\\DateTime', $this->typeSystem->getTypeFromTypeHint('\\DateTime'));
        $this->assertEqualsObjectType('\\DateTime', $this->typeSystem->getTypeFromTypeHint('\\dAtEtImE'));
    }

    protected function assertCommonAncestor(IType $ancestor, IType $type1, IType $type2)
    {
        $this->assertEqualTypes($ancestor, $this->typeSystem->getCommonAncestorType($type1, $type2));
        $this->assertEqualTypes($ancestor, $this->typeSystem->getCommonAncestorType($type2, $type1));
    }

    protected function getObjectType($class)
    {
        if(is_array($class)) {
            return $this->typeSystem->getCompositeType(array_map([$this->typeSystem, 'getObjectType'], $class));
        } else {
            return $this->typeSystem->getObjectType($class);
        }
    }

    protected function assertObjectCommonAncestor($ancestor, $class1, $class2)
    {
        $this->assertCommonAncestor(
                $this->getObjectType($ancestor),
                $this->getObjectType($class1),
                $this->getObjectType($class2)
        );
    }

    public function testCommonAncestorResolution()
    {
        $mixed = $this->typeSystem->getNativeType(INativeType::TYPE_MIXED);
        $this->assertCommonAncestor($mixed,
                $this->typeSystem->getNativeType(INativeType::TYPE_MIXED),
                $this->typeSystem->getNativeType(INativeType::TYPE_STRING)
        );

        $this->assertCommonAncestor(
                $this->typeSystem->getNativeType(INativeType::TYPE_STRING),
                $this->typeSystem->getNativeType(INativeType::TYPE_STRING),
                $this->typeSystem->getNativeType(INativeType::TYPE_STRING)
        );

        $this->assertCommonAncestor($mixed,
                $this->typeSystem->getNativeType(INativeType::TYPE_STRING),
                $this->typeSystem->getObjectType('stdClass')
        );

        $this->assertObjectCommonAncestor('stdClass', 'stdClass', 'stdClass');

        $this->assertObjectCommonAncestor(
                ITraversable::ITRAVERSABLE_TYPE,
                ICollection::ICOLLECTION_TYPE,
                ITraversable::ITRAVERSABLE_TYPE
        );

        $this->assertObjectCommonAncestor(
                'IteratorAggregate',
                'IteratorAggregate',
                IRepository::IREPOSITORY_TYPE
        );

        $this->assertObjectCommonAncestor(
                ['IteratorAggregate', 'Iterator'],
                ['IteratorAggregate', 'Iterator', 'Traversable'],
                IRepository::IREPOSITORY_TYPE
        );

        $this->assertObjectCommonAncestor('Traversable', 'Traversable', 'ArrayObject');

        $this->assertObjectCommonAncestor(
                ['ArrayAccess', 'Countable', 'Serializable', 'Traversable'],
                'ArrayObject',
                'ArrayIterator'
        );

        $this->assertObjectCommonAncestor('Iterator', 'SeekableIterator', 'RecursiveIterator');

        $this->assertObjectCommonAncestor(PHP_VERSION_ID >= 80000 ? 'IteratorAggregate' :'Traversable', 'ArrayObject', 'DatePeriod');
    }

    public function testFunction()
    {
        foreach(['strlen', '\\strlen', 'StrLEN', '\\stRlen'] as $strlenName) {
            $function = $this->typeSystem->getFunction($strlenName);
            $this->assertSame('strlen', $function->getName());
            $this->assertSame('strlen', $function->getReflection()->getName());
            $this->assertSame($this->typeSystem, $function->getTypeSystem());
            $this->assertEqualsNativeType(INativeType::TYPE_INT, $function->getReturnType());
            $this->assertEqualsNativeType(INativeType::TYPE_INT, $function->getReturnTypeWithArguments(['abc']));
            $this->assertEqualsNativeType(INativeType::TYPE_INT, $function->getReturnTypeWithArguments(['sdsscsc']));
        }
    }

    public function testClass()
    {
        foreach(['stdClass', '\\stdClass', 'stdCLASS', '\\sTDClass'] as $stdClassName) {
            $class = $this->typeSystem->getObjectType($stdClassName);
            $this->assertSame('stdClass', $class->getClassType());
            $this->assertSame('stdClass', $class->getReflection()->getName());
            $constructor = $class->getConstructor(O\Expression::newExpression(O\Expression::value($stdClassName)));
            $this->assertSame($this->typeSystem, $constructor->getTypeSystem());
            $this->assertEqualTypes($this->typeSystem->getObjectType('stdClass') , $constructor->getReturnType());
            $this->assertEqualTypes($this->typeSystem->getObjectType('stdClass') , $constructor->getSourceType());
            $this->assertSame(false , $constructor->hasMethod());
            $this->assertSame(null, $constructor->getReflection());
        }
    }

    public function testClassMembers()
    {
        $class = $this->typeSystem->getObjectType('DateInterval');
        $this->assertSame('DateInterval', $class->getClassType());
        $this->assertSame('DateInterval', $class->getReflection()->getName());

        $method = $class->getMethod(O\Expression::methodCall(O\Expression::value(''), O\Expression::value('FORmat')));
        $this->assertSame('format', $method->getName());
        $this->assertSame($this->typeSystem, $method->getTypeSystem());
        $this->assertEqualsNativeType(INativeType::TYPE_STRING , $method->getReturnType());
        $this->assertEqualsNativeType(INativeType::TYPE_STRING , $method->getReturnTypeWithArguments(['ssd']));
        $this->assertEqualsObjectType('DateInterval', $method->getSourceType());
        $this->assertSame('format', $method->getReflection()->getName());

        $field = $class->getField(O\Expression::field(O\Expression::value(''), O\Expression::value('y')));
        $this->assertSame('y', $field->getName());
        $this->assertSame(false, $field->isStatic());
    }

    public function testFieldsAreCaseSensitive()
    {
        $this->expectException(TypeException::class);
        $class = $this->typeSystem->getObjectType('DateInterval');
        $class->getField(O\Expression::field(O\Expression::value(''), O\Expression::value('T')));
    }

    public function testArray()
    {
        $array = $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY);
        $indexer = $array->getIndex(O\Expression::index(O\Expression::value([]), O\Expression::value('s')));
        $this->assertSame($this->typeSystem, $indexer->getTypeSystem());
        $this->assertEqualsNativeType(INativeType::TYPE_ARRAY, $indexer->getSourceType());
        $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $indexer->getReturnType());
        if($indexer instanceof IIndexer) {
            $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $indexer->getReturnTypeOfIndex(3));
            $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $indexer->getReturnTypeOfIndex('abc'));
        }
    }

    public function testCompositeType()
    {
        $compositeType = $this->typeSystem->getType(TypeId::getComposite([TypeId::getObject('ArrayAccess'), TypeId::getObject('Countable')]));

        $indexer = $compositeType->getIndex(O\Expression::index(O\Expression::value([]), O\Expression::value('s')));
        $this->assertEqualTypes($this->typeSystem->getObjectType('ArrayAccess'), $indexer->getSourceType());
        $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $indexer->getReturnType());

        $method = $compositeType->getMethod(O\Expression::methodCall(O\Expression::value([]), O\Expression::value('offsetExists')));
        $this->assertEqualTypes($this->typeSystem->getObjectType('ArrayAccess'), $method->getSourceType());
        $this->assertEqualsNativeType(INativeType::TYPE_BOOL, $method->getReturnType());

        $method = $compositeType->getMethod(O\Expression::methodCall(O\Expression::value([]), O\Expression::value('count')));
        $this->assertEqualTypes($this->typeSystem->getObjectType('Countable'), $method->getSourceType());
        $this->assertEqualsNativeType(INativeType::TYPE_INT, $method->getReturnType());
    }

    public function testRegisteringTypeDataModules()
    {
        if($this->typeSystem instanceof PhpTypeSystem) {
            $typeDataModule = new TypeDataModule(
                    [__CLASS__ => ['methods' => ['assertEquals' => INativeType::TYPE_NULL]]],
                    ['get_defined_functions' => INativeType::TYPE_INT]
            );

            $this->assertNotContains($typeDataModule, $this->typeSystem->getTypeDataModules());
            $this->typeSystem->registerTypeDataModule($typeDataModule);
            $this->assertContains($typeDataModule, $this->typeSystem->getTypeDataModules());

            $this->assertEqualsNativeType(
                    INativeType::TYPE_NULL,
                    $this->typeSystem->getObjectType(__CLASS__)->getMethod(
                            O\Expression::methodCall(O\Expression::value($this), O\Expression::value('assertEquals'))
                    )->getReturnType()
            );

            $this->assertEqualsNativeType(
                    INativeType::TYPE_INT,
                    $this->typeSystem->getFunction('get_defined_functions')->getReturnType()
            );
        }
    }
}