<?php

namespace Pinq\Tests\Integration\Collection;

class CustomException extends \Exception { const CustomExceptionType = __CLASS__; }

abstract class CollectionTest extends \Pinq\Tests\Integration\DataTest
{
    final protected function AssertThatExecutionIsNotDeferred(callable $CollectionOperation) 
    {
        $Exception = new CustomException();
        
        
        $ThowingFunction = function () use ($Exception) { throw $Exception; };
        
        try {
            $CollectionOperation($ThowingFunction);
        }
        catch (CustomException $ThrownException) {
            $this->assertSame($Exception, $ThrownException);
        }
    }
    
    final protected function ImplementationsFor(array $Data)
    {
        return [
            [new \Pinq\Collection($Data), $Data],
            [(new \Pinq\Collection($Data))->AsRepository(), $Data],
        ];
    }
}
