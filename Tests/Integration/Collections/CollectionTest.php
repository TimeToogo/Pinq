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

    final protected function AssertMatches(\Pinq\ICollection $Collection, array $Array, $Name = '')
    {
        $this->assertEquals(!empty($Array), $Collection->Exists(), $Name . ' - Both empty or non empty -');
        $this->assertSameSize($Array, $Collection, $Name . ' - Same size -');
        $this->assertEquals($Array, $Collection->AsArray(), $Name . ' - Result Equal -');
    }
}
