<?php

namespace Pinq\Tests\Integration\Traversable;

class CustomException extends \Exception {}

abstract class TraversableTest extends \Pinq\Tests\Integration\DataTest
{
    
    final protected function AssertThatExecutionIsDeferred(callable $TraversableQuery)
    {
        $Exception = new CustomException();
        
        $ThowingFunction = function () use ($Exception) { throw $Exception; };
        
        try {
            $Traversable = $TraversableQuery($ThowingFunction);
        }
        catch (CustomException $ThrownException) {
            $this->assertFalse(true, 'Traversable query method should not have thrown exception');
        }
        
        try {
            foreach ($Traversable as $Key => $Value) {
                $this->assertFalse(true, 'Iteration should have thrown an exception');
            }
        } 
        catch (CustomException $ThrownException) {
            $this->assertSame($Exception, $ThrownException);
        }
    }
    
    
    final protected function ImplementationsFor(array $Data)
    {
        return [
            [new \Pinq\Traversable($Data), $Data],
            [(new \Pinq\Traversable($Data))->AsCollection(), $Data],
            [(new \Pinq\Traversable($Data))->AsQueryable(), $Data],
            [(new \Pinq\Traversable($Data))->AsRepository(), $Data],
        ];
    }
}
