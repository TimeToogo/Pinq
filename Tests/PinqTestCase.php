<?php 

namespace Pinq\Tests;

/**
 * The base class for all pinq test cases
 */
class PinqTestCase extends \PHPUnit_Framework_TestCase
{
    const TEST_NAMESPACE = __NAMESPACE__;
    const ROOT_NAMESPACE = '\\Pinq\\';
    
    protected final function getMockWithoutConstructor($className)
    {
        return $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
    }
    
    protected final function getAbstractMockWithoutConstructor($className)
    {
        return $this->getMockBuilder($className)->disableOriginalConstructor()->getMockForAbstractClass();
    }
}