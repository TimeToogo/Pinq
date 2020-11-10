<?php

namespace Pinq\Tests\Integration\Queries;

use PHPUnit\Framework\Constraint\IsEqual;
use Pinq\Queries\IParameterRegistry;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

/**
 * Comparator to assert whether queries are equal.
 * Ignores parameter ids.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class QueryComparator extends Comparator
{
    const PARAMETER_NAME = '~~~PARAMETER~~~';
    private static $number = 0;

    public static function parameter()
    {
        return self::PARAMETER_NAME . self::$number++;
    }

    public static function isParameter($value)
    {
        return is_string($value) && strpos($value, self::PARAMETER_NAME) === 0;
    }

    public function accepts($expected, $actual)
    {
        if ((is_string($expected) && is_string($actual) && QueryComparator::isParameter($expected))
                || ($expected instanceof IParameterRegistry && $actual instanceof IParameterRegistry)
        ) {
            return true;
        } elseif (is_array($expected) && is_array($actual) && !empty($expected)) {
            foreach ($expected as $key => $value) {
                if (!self::isParameter($key)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function assertEquals($expected, $actual, $delta = 0, $canonicalize = false, $ignoreCase = false)
    {
        if (is_array($expected)) {
            $this->factory->getComparatorFor(array_values($expected), array_values($actual))
                    ->assertEquals(
                            array_values($expected),
                            array_values($actual)
                    );
        }
    }

    public static function assertEqualsButIgnoreParameterIds($expected, $actual)
    {
        $comparator = new self();
        $factory    = ComparatorFactory::getInstance();
        $comparator->setFactory($factory);
        $factory->register($comparator);

        $constraint = new IsEqual($expected);

        try {
            $constraint->evaluate($actual, 'Queries must be equivalent');
        } catch (\Exception $exception) {
            $factory->unregister($comparator);
            throw $exception;
        }

        $factory->unregister($comparator);
    }
}