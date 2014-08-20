<?php

namespace Pinq\Tests;

/**
 * The base class for all Pinq test cases
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PinqTestCase extends \PHPUnit_Framework_TestCase
{
    const TEST_NAMESPACE = __NAMESPACE__;
    const ROOT_NAMESPACE = '\\Pinq\\';

    final protected function assertReferenceEquals(&$expectedRef, &$otherRef, $message = 'References must be equal')
    {
        $this->assertSame($expectedRef, $otherRef, $message);

        $originalValue = $expectedRef;

        $instance = new \stdClass();
        $expectedRef = $instance;

        $this->assertSame($instance, $otherRef, $message);

        $expectedRef = $originalValue;
    }
}
