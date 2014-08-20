<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class ComplexConverterTest extends InterpreterTest
{
    /**
     * @dataProvider interpreters
     */
    public function testDefaultOrderOfBinaryOperationFunction()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];

        $this->assertRecompilesCorrectly(
                function ($i) {
                    return $i * -$i + 5 ^ $i / -$i % $i + 2 << $i - +$i;
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedVariableResolution()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        $factor = 5;

        $this->assertRecompilesCorrectly(
                function ($i) use ($factor) {
                    return $i * $factor;
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testNestedClosureUsedVariableResolution()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        $factor = 5;

        $this->assertRecompilesCorrectly(
                function ($i) use ($factor) {
                    $innerClosure =
                            function ($i) use ($factor) {
                                return $i * $factor;
                            };

                    return $innerClosure($i);
                },
                $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testVariableReturnValueResolution()
    {
        foreach ([1, null, 'test', false, new \stdClass(), [1234567890, 'tests', new \stdClass(), function () {}]] as $value) {
            $this->assertScopedVariables(
                    function () use ($value) {
                        return $value;
                    },
                    ['value' => $value]);
        }
    }

    /**
     * @dataProvider interpreters
     */
    public function testNestedClosure()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];

        $this->assertRecompilesCorrectly(
                function ($i) {
                    $divider =
                            function () use ($i) {
                                return $i / 5;
                            };

                    return $divider();
                },
                $valueSet);
    }

    /** ---- Some code from the wild ---- **/
    /**
     * @dataProvider interpreters
     * @link http://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
     */
    public function testFileSizeFormatter()
    {
        $formatter = function ($bytes, $precision = 2) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');

            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);

            // Uncomment one of the following alternatives
            // $bytes /= pow(1024, $pow);
            // $bytes /= (1 << (10 * $pow));
            return round($bytes, $precision) . ' ' . $units[$pow];
        };

        $valueSet = [[0], [1000], [500050], [323241234], [5000000]];

        $this->assertRecompilesCorrectly($formatter, $valueSet);
    }
}
