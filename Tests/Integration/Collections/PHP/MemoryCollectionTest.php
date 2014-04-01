<?php

namespace Pinq\Tests\Integration\Collections\PHP;

abstract class MemoryCollectionTest extends \Pinq\Tests\Integration\Collections\CollectionTest
{
    protected $ArrayData;

    public function __construct()
    {
        parent::__construct();
    }

    final protected function Collection()
    {
        $this->ArrayData = $this->ArrayData();

        return new \Pinq\Collection($this->ArrayData);
    }

    abstract protected function ArrayData();

    public function testThatUnionRemovesDuplicates()
    {
        $Collection = $this->Collection->Union($this->Collection);

        $this->AssertMatches($Collection, $this->ArrayData, 'Union');
    }

    public function testThatWhereTrueReturnsTheSameCollection()
    {
        $Collection = $this->Collection->Where(function () { return true; });

        $this->AssertMatches($Collection, $this->ArrayData);
    }


    public function testThatWhereFaleReturnsAnEmptyCollection()
    {
        $Collection = $this->Collection->Where(function () { return false; });

        $this->AssertMatches($Collection, []);
    }

    public function testThatAppendMergesDuplicates()
    {
        $Collection = $this->Collection->Append($this->Collection);

        $this->AssertMatches($Collection, array_merge($this->ArrayData, $this->ArrayData), 'Append');
    }

    public function testThatIntersectingWithTheSameCollectionReturnsTheEquivalentCollection()
    {
        $Collection = $this->Collection->Intersect($this->Collection);

        $this->AssertMatches($Collection, $this->ArrayData, 'Intersect');
    }

    public function testThatExceptTheSameCollectionRemoveAllValuesFromTheCollection()
    {
        $Collection = $this->Collection->Except($this->Collection);

        $this->AssertMatches($Collection, [], 'Except');
    }
}
