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
        $thowingFunction =
                function () use ($exception) {
                    throw $exception;
                };

        try {
            $traversable = $traversableQuery($thowingFunction);
        } catch (CustomException $thrownException) {
            $this->assertFalse(
                    true,
                    'Traversable query method should not have thrown exception');
        }

        try {
            foreach ($traversable as $key => $value) {
                $this->assertFalse(true, 'Iteration should have thrown an exception');
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
    
    final protected function assertReturnsOriginalType(\Pinq\ITraversable $traversable, $queryMethod, $argument)
    {
        $originalType = get_class($traversable);
        
        $this->assertSame($originalType, get_class($traversable->$queryMethod($argument)));
    }

    /**
     * @dataProvider everything
     */
    final public function testThatReturnsNewInstanceOfSameType(\Pinq\ITraversable $traversable, array $data)
    {
        $originalType = get_class($traversable);
        $returnedTraversable = $this->_testReturnsNewInstanceOfSameType($traversable);

        if ($returnedTraversable === self::$instance) {
            return;
        }

        $this->assertInstanceOf(
                \Pinq\ITraversable::ITRAVERSABLE_TYPE,
                $returnedTraversable);
        $this->assertNotSame($traversable, $returnedTraversable);
        $this->assertSame($originalType, get_class($returnedTraversable));
    }

    private static $instance;

    protected function _testReturnsNewInstanceOfSameType(\Pinq\ITraversable $traversable)
    {
        if (self::$instance === null) {
            self::$instance = new \stdClass();
        }

        return self::$instance;
    }

    final protected function implementationsFor(array $data)
    {
        return [
            [new \Pinq\Traversable($data), $data], 
            [new \Pinq\Collection($data), $data], 
            [(new \Pinq\Providers\Traversable\Provider(new \Pinq\Traversable($data)))->createQueryable(), $data],
            [(new \Pinq\Providers\Collection\Provider(new \Pinq\Collection($data)))->createRepository(), $data],
        ];
    }
}
