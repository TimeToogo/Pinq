<?php

namespace Pinq\Tests\Integration\Collection;

class CustomException extends \Exception
{
    const CUSTOM_EXCEPTION_TYPE = __CLASS__;
}

abstract class CollectionTest extends \Pinq\Tests\Integration\DataTest
{
    final protected function assertThatExecutionIsNotDeferred(callable $collectionOperation)
    {
        $exception = new CustomException();
        $thowingFunction =
                function () use ($exception) {
                    throw $exception;
                };
                
        $thrown = false;
        try {
            $collectionOperation($thowingFunction);
        } catch (CustomException $thrownException) {
            $thrown = true;
            $this->assertSame($exception, $thrownException);
        }
        
        $this->assertTrue($thrown, 'Should have thrown an exeption');
    }

    final protected function implementationsFor(array $data)
    {
        return [[new \Pinq\Collection($data), $data], [(new \Pinq\Collection($data))->asRepository(), $data]];
    }
}
