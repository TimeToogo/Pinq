<?php

namespace Pinq\Tests\Integration\Collections;

abstract class CollectionTest extends \Pinq\Tests\PinqTestCase
{
    /**
     * @var \Pinq\ICollection
     */
    protected $Collection;

    public function __construct()
    {
        parent::__construct();
    }

    abstract protected function Collection();

    protected function setUp()
    {
        $this->Collection = $this->Collection();
    }

    final protected function AssertMatches(\Pinq\ITraversable $Collection, array $Array, $Name = '')
    {
        $this->assertSame($Array, $Collection->AsArray(), $Name);
    }
}
