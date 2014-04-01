<?php

namespace Pinq\Tests;

/**
 * The base class for all pinq test cases
 */
class PinqTestCase extends \PHPUnit_Framework_TestCase
{
    const TestNamespace = __NAMESPACE__;

    const RootNamespace = '\\Pinq\\';

    final protected function getMockWithoutConstructor($ClassName)
    {
        return $this->getMockBuilder($ClassName)
                ->disableOriginalConstructor()
                ->getMock();
    }
    final protected function getAbstractMockWithoutConstructor($ClassName)
    {
        return $this->getMockBuilder($ClassName)
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
    }
}
