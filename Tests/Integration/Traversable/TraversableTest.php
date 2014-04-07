<?php

namespace Pinq\Tests\Integration\Traversable;

abstract class TraversableTest extends \Pinq\Tests\Integration\DataTest
{
    
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
