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

    /**
     * @dataProvider Everything
     */
    final public function testThatReturnsNewInstance(\Pinq\ITraversable $traversable, array $data)
    {
        $returnedTraversable = $this->_testReturnsNewInstance($traversable);

        if ($returnedTraversable === self::$instance) {
            return;
        }

        $this->assertInstanceOf(
                \Pinq\ITraversable::ITRAVERSABLE_TYPE,
                $returnedTraversable);
        $this->assertNotSame($traversable, $returnedTraversable);
    }

    private static $instance;

    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        if (self::$instance === null) {
            self::$instance = new \stdClass();
        }

        return self::$instance;
    }

    final protected function implementationsFor(array $data)
    {
        return [[new \Pinq\Traversable($data), $data], [(new \Pinq\Traversable($data))->asCollection(), $data], [(new \Pinq\Traversable($data))->asQueryable(), $data], [(new \Pinq\Traversable($data))->asRepository(), $data]];
    }
}
