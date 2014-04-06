<?php

namespace Pinq\Tests\Integration\Traversable;

class IndexByTest extends TraversableTest
{
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatIndexByElementIndexesCorrectly(\Pinq\ITraversable $Traversable, array $Data)
    {
        $IndexedElements = $Traversable->IndexBy(function ($I) { return $I; });
        
        $this->AssertMatches($IndexedElements, array_combine($Data, $Data));
    }
    /**
     * @dataProvider Everything
     */
    public function testThatIndexByNullReturnsLastArrayWithLastElement(\Pinq\ITraversable $Traversable, array $Data)
    {
        $IndexedElements = $Traversable->IndexBy(function () { return null; });
        
        $this->AssertMatches($IndexedElements, empty($Data) ? [] :[null => end($Data)]);
    }
}
