<?php 

namespace Pinq\Tests\Integration\Collection;

class CustomException extends \Exception
{
    const CUSTOM_EXCEPTION_TYPE = __CLASS__;
}

abstract class CollectionTest extends \Pinq\Tests\Integration\DataTest
{
    protected final function assertThatExecutionIsNotDeferred(callable $collectionOperation)
    {
        $exception = new CustomException();
        $thowingFunction = 
                function () use($exception) {
                    throw $exception;
                };
        
        try {
            $collectionOperation($thowingFunction);
        }
        catch (CustomException $thrownException) {
            $this->assertSame($exception, $thrownException);
        }
    }
    
    protected final function implementationsFor(array $data)
    {
        return [[new \Pinq\Collection($data), $data], [(new \Pinq\Collection($data))->asRepository(), $data]];
    }
}