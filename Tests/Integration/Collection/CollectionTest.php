<?php

namespace Pinq\Tests\Integration\Collection;

abstract class CollectionTest extends \Pinq\Tests\Integration\DataTest
{
    
    final protected function ImplementationsFor(array $Data)
    {
        return [
            [new \Pinq\Collection($Data), $Data],
            [(new \Pinq\Collection($Data))->AsRepository(), $Data],
        ];
    }
}
