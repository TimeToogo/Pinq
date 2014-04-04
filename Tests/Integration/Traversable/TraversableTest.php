<?php

namespace Pinq\Tests\Integration\Traversable;

abstract class TraversableTest extends \Pinq\Tests\PinqTestCase
{
    public function __construct()
    {
        parent::__construct();
    }
    
    final protected function AssertMatches(\Pinq\ITraversable $Collection, array $Array, $Message = '')
    {
        $this->assertEquals($Array, $Collection->AsArray(), $Message);
    }
    
    final protected function AssertMatchesValues(\Pinq\ITraversable $Collection, array $Array, $Message = '')
    {
        $this->assertEquals(array_values($Array), array_values($Collection->AsArray()), $Message);
    }
}
