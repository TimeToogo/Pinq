<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\INativeType;
use Pinq\ITraversable;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ComplexExpressionAnalysisTest extends ExpressionAnalysisTestCase
{
    public function testExampleFromDocs()
    {
        $this->assertReturnsNativeType(
                function (ITraversable $traversable) {
                    $traversable
                            ->where(function (array $row) { return $row['age'] <= 50; })
                            ->orderByAscending(function (array $row) { return $row['firstName']; })
                            ->thenByAscending(function (array $row) { return $row['lastName']; })
                            ->take(50)
                            ->indexBy(function (array $row) { return $row['phoneNumber']; })
                            ->select(function (array $row) {
                                return [
                                        'fullName'    => $row['firstName'] . ' ' . $row['lastName'],
                                        'address'     => $row['address'],
                                        'dateOfBirth' => $row['dateOfBirth'],
                                ];
                            })
                            ->implode(':', function (array $row) { return $row['fullName']; });
                },
                INativeType::TYPE_STRING
        );
    }

    public function testReferences()
    {
        $this->assertReturnsNativeType(
                function (\stdClass $foo) {
                    $var  =& $foo;
                    $bar  =& $var;
                    $abcd =& $bar;
                    $dsc  =& $bar;
                    $bar  = 'abc';

                    return $foo;
                },
                INativeType::TYPE_STRING
        );

        $this->assertReturnsNativeType(
                function (\stdClass $foo) {
                    $var  =& $foo;
                    $bar  =& $var;
                    $abcd =& $bar;
                    $dsc  =& $bar;
                    $bar  = 'abc';

                    return $abcd;
                },
                INativeType::TYPE_STRING
        );

        $this->assertReturnsNativeType(
                function (\stdClass $foo) {
                    $var  =& $foo;
                    $bar  =& $var;
                    $abcd =& $bar;
                    $dsc  =& $bar;
                    $foo  = 3.42;

                    return $dsc;
                },
                INativeType::TYPE_DOUBLE
        );


        $this->assertReturnsNativeType(
                function (array $foo) {
                    $var  =& $foo;
                    $bar = 3.42;
                    $var  =& $bar;

                    return $foo;
                },
                INativeType::TYPE_ARRAY
        );
    }
}