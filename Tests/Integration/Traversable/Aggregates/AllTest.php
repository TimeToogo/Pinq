<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AllTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider emptyData
     */
    public function testThatAllReturnsTrueIfEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertTrue($traversable->all());
    }

    public function falseyValues()
    {
        //                                                      V
        return $this->getImplementations([1,1,1,1,1,1,10,11,1,1,0,1,1,1,1,]) +
                //                                                                          V
                $this->getImplementations(['ert','rgrg', 'dgf', 'g4g43', 'as', 'vd', 'dw', '', 'saav']) +
                //                                 V
                $this->getImplementations([true, false, true]);
    }

    /**
     * @dataProvider falseyValues
     */
    public function testThatAllReturnsFalseIfThereIsAFalsyValue(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertFalse($traversable->all());
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatAllReturnsTrueWhenAllElementMatch(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertTrue($traversable->all(function ($i) { return $i > -5; }));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatAllReturnsFalseWhenNotAllElementMatch(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertFalse($traversable->all(function ($i) { return $i > 5; }));
    }

    /**
     * @dataProvider everything
     */
    public function testThatAllOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertSame(
                empty($data) ?: count(array_filter($data)) === count($data),
                $traversable->all());
    }
}
