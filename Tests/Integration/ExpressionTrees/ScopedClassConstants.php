<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

class ParentScopeClass
{

}

class ScopedClassConstants extends ParentScopeClass
{
    public function concreteClass()
    {
        return function () { return [\DateTime::class, \DatetiME::claSs]; };
    }

    public function selfClass()
    {
        return function () { return [self::class, SELF::CLASS]; };
    }

    public function parentClass()
    {
        return function () { return [parent::class, PARENT::CLASS]; };
    }

    public function staticClass()
    {
        return function () { return [static::class, STATIC::CLASS]; };
    }

    //Only self::class does not throw a fatal error
    public function selfParameter()
    {
        return function ($i = [self::class, SELF::CLASS]) { return $i; };
    }

    public function selfParameterComplex()
    {
        return function ($i = [self::class => [1,2,3, Self::CLaSS], \DateTime::class, \DateTIME::Class]) { return $i; };
    }
}

class StaticScopeClassConstants extends ScopedClassConstants
{

}