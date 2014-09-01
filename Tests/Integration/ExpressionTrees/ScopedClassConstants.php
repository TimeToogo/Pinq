<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class ParentScopeClass
{

}

class ScopedClassConstants extends ParentScopeClass
{
    const TYPE = __CLASS__;

    public function concreteClass()
    {
        return function () { return [\DateTime::class, \DatetiME::class]; };
    }

    public function selfClass()
    {
        return function () { return [self::class, SELF::class]; };
    }

    public static function selfClassStaticMethod()
    {
        return [self::class, SELF::class];
    }

    public function parentClass()
    {
        return function () { return [parent::class, PARENT::class]; };
    }

    public static function parentClassStaticMethod()
    {
        return [self::class, SELF::class];
    }

    public function staticClass()
    {
        return function () { return [static::class, STATIC::class]; };
    }

    public static function staticClassStaticMethod()
    {
        return [static::class, STATIC::class];
    }

    //Only self::class does not throw a fatal error
    public function selfParameter()
    {
        return function ($i = [self::class, SELF::class]) { return $i; };
    }

    public function selfParameterComplex()
    {
        return function ($i = [self::class => [1,2,3, Self::class], \DateTime::class, \DateTIME::class]) { return $i; };
    }
}

class StaticScopeClassConstants extends ScopedClassConstants
{

}
