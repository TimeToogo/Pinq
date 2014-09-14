<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\IIndexer;
use Pinq\Analysis\INativeType;
use Pinq\Analysis\IType;
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
                INativeType::TYPE_BOOL  => true,
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

        $this->assertObjectCommonAncestor('Traversable', 'Iterator', ['IteratorAggregate', 'ArrayObject', 'ArrayObject']);

        $this->assertObjectCommonAncestor('Traversable', 'ArrayObject', 'DatePeriod');
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

    /**
     * @expectedException \Pinq\Analysis\TypeException
     */
    public function testFieldsAreCaseSensitive()
    {
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
} 