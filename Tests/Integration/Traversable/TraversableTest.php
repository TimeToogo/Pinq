<?php

namespace Pinq\Tests\Integration\Traversable;

class CustomException extends \Exception
{

}

abstract class TraversableTest extends \Pinq\Tests\Integration\DataTest
{
    final protected function assertThatExecutionIsDeferred(callable $traversableQuery)
    {
        $exception = new CustomException();
        $throwingFunction =
                function () use ($exception) {
                    throw $exception;
                };

        try {
            $traversable = $traversableQuery($throwingFunction);
        } catch (CustomException $thrownException) {
            $this->assertFalse(
                    true,
                    'Traversable query method should not have thrown the exception');
        }

        try {
            foreach ($traversable as $key => $value) {
                $this->assertFalse(true, 'Iteration should caused the exception to be thrown');
            }
        } catch (CustomException $thrownException) {
            $this->assertSame($exception, $thrownException);
        }
    }

    final protected function assertThatCalledWithValueAndKeyParametersOnceForEachElementInOrder(callable $traversableQuery, array $data, $returnValue = null)
    {
        reset($data);
        $traversable = $traversableQuery(function ($value, $key) use (&$data, $returnValue) {
            $this->assertSame(current($data), $value, 'value must match');
            $this->assertSame(key($data), $key, 'key must match');
            next($data);

            return $returnValue;
        });

        $traversable->asArray();
    }

    /**
     * @dataProvider theImplementations
     */
    final public function testThatReturnsNewInstanceOfCorrectTypeWithSameScheme(\Pinq\ITraversable $traversable, array $data)
    {
        $originalType = get_class($traversable);
        $originalScheme = $traversable->getIteratorScheme();

        $returnedTraversable = $this->_testReturnsNewInstanceOfSameTypeWithSameScheme($traversable);

        if ($returnedTraversable === self::$instance) {
            return;
        }

        $this->assertNotSame($traversable, $returnedTraversable);
        if ($traversable instanceof \Pinq\IQueryable) {
            $this->assertInstanceOf(
                    \Pinq\IQueryable::IQUERYABLE_TYPE,
                    $returnedTraversable);
        } else {
            $this->assertInstanceOf(
                    \Pinq\IQueryable::ITRAVERSABLE_TYPE,
                    $returnedTraversable);
        }
        $this->assertSame($originalScheme, $returnedTraversable->getIteratorScheme());
    }

    private static $instance;

    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        if (self::$instance === null) {
            self::$instance = new \stdClass();
        }

        return self::$instance;
    }

    final protected function implementationsFor(array $data)
    {
        $implementations = [];
        foreach (\Pinq\Iterators\SchemeProvider::getAvailableSchemes() as $scheme) {
            $implementations = array_merge($implementations, [
                [new \Pinq\Traversable($data, $scheme), $data],
                [(new \Pinq\Providers\Traversable\Provider(new \Pinq\Traversable($data, $scheme)))->createQueryable(), $data]
            ]);
        }

        return $implementations;
    }
}
