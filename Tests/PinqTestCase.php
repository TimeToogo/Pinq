<?php

namespace Pinq\Tests;

/**
 * The base class for all pinq test cases
 */
class PinqTestCase extends \PHPUnit_Framework_TestCase
{
    const TEST_NAMESPACE = __NAMESPACE__;
    const ROOT_NAMESPACE = '\\Pinq\\';

    final protected function getMockWithoutConstructor($className)
    {
        return $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
    }

    final protected function getAbstractMockWithoutConstructor($className)
    {
        return $this->getMockBuilder($className)->disableOriginalConstructor()->getMockForAbstractClass();
    }
}
