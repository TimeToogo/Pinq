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
        $implementations = [];
        foreach (\Pinq\Iterators\SchemeProvider::getAvailableSchemes() as $scheme) {
            $implementations = array_merge($implementations, [
                [new \Pinq\Collection($data, $scheme), $data],
                [(new \Pinq\Providers\Collection\Provider(new \Pinq\Collection($data, $scheme)))->createRepository(), $data]
            ]);
        }

        return $implementations;
    }
}
