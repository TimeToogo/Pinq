<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\IQueryable;
use Pinq\IRepository;
use Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors\StructuralVariableProcessor;

class EnglishDSLProviderWithStructuralVariableInliningTest extends DSLCompilationProviderTest
{
    protected function compilerConfiguration()
    {
        return new Implementation\English\Configuration();
    }

    protected function structuralExpressionProcessors()
    {
        return [new StructuralVariableProcessor()];
    }

    /**
     * @dataProvider allImplementations
     */
    public function testVariableVariableWhereQuery()
    {
        foreach(['i', 'o'] as $varName) {
            $this->assertRequestIsCorrect(
                    function (IQueryable $queryable) use ($varName) {
                        return $queryable->where(function ($i, $o) use ($varName) { return $$varName; });
                    },
                    <<<ENG
Filter according to: { return $$varName; } with parameters: [\$this, \$varName]
Get the elements as itself
ENG
            );
        }
    }

    /**
     * @dataProvider repositories
     */
    public function testVariableVariableApplyQuery()
    {
        foreach(['fff', 'bbbbb'] as $varName) {
            $this->assertOperationIsCorrect(
                    function (IRepository $repository) use ($varName) {
                        $repository
                                ->take(5)
                                ->join([])
                                ->apply(function (&$i, $o) use ($varName) { $i /= $$varName; });
                    },
                    <<<ENG
Starting from and up to the specified element
Join with: [array or iterator] and update the outer values according to: { \$i /= $$varName; } with parameters: [\$this, \$varName]
ENG
            );
        }
    }

    /**
     * @dataProvider allImplementations
     */
    public function testVariableVariablesJoinToSubScope()
    {
        foreach([['a', 'b'], ['dd', 'ee'], ['abc', 'qw112']] as $varNames) {
            $this->assertRequestIsCorrect(
                    function (IQueryable $queryable) use ($varNames) {
                        return $queryable
                                ->join($queryable
                                        ->orderByAscending(function ($i) use ($varNames) { return ${$varNames[0]}; })
                                        ->select(function ($i) use ($varNames) { return ${$varNames[1]}; })
                                        ->take(50))
                                ->to(function ($o, $i) use ($varNames) { return [${$varNames[0]}, ${$varNames[1]}]; });
                    },
                    <<<ENG
Join with: [
    Order according to: { return $$varNames[0]; } with parameters: [\$this, \$varNames] asc or desc
    Map according to: { return $$varNames[1]; } with parameters: [\$this, \$varNames]
    Starting from and up to the specified element
] and correlate the values according to: { return [$$varNames[0], $$varNames[1]]; } with parameters: [\$this, \$varNames]
Get the elements as itself
ENG
            );
        }
    }

    private $privateVarName = 'PRIVATE_PROPERTY_VAR_NAME';

    public  $publicVarName = 'PUBLIC_PROPERTY_VAR_NAME';

    private static $privateStaticVarName = 'PRIVATE_STATIC_PROPERTY_VAR_NAME';

    public static $publicStaticVarName = 'PUBLIC_STATIC_PROPERTY_VAR_NAME';

    private function privateVariableName()
    {
        return 'PRIVATE_METHOD_VAR_NAME';
    }

    public function publicVariableName()
    {
        return 'PUBLIC_METHOD_VAR_NAME';
    }

    private function prefixedName($name)
    {
        return 'PREFIX_' . $name;
    }

    /**
     * @dataProvider allImplementations
     */
    public function testExampleFromDocsWithVariableVariableInlining()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    return $queryable
                            ->where(function ($row) { return ${$this->privateVariableName()}['age'] <= 50; })
                            ->orderByAscending(function ($row) { return ${$this->publicVariableName()}['firstName']; })
                            ->thenByAscending(function ($row) { return ${$this->privateVarName}['lastName']; })
                            ->take(50)
                            ->indexBy(function ($row) { return ${$this->publicVarName}['phoneNumber']; })
                            ->select(function ($row) {
                                return [
                                    'fullName' => ${self::$privateStaticVarName}['firstName'] . ' ' . ${self::$publicStaticVarName}['lastName'],
                                    'address' => ${$this->prefixedName('foo')}['address'],
                                    'dateOfBirth' => ${$this->prefixedName('_A_' . '_B_' . self::$privateStaticVarName)}['dateOfBirth'],
                                ];
                            });
                },
                <<<'ENG'
Filter according to: { return ($PRIVATE_METHOD_VAR_NAME['age'] <= 50); } with parameters: [$this]
Order according to: { return $PUBLIC_METHOD_VAR_NAME['firstName']; } with parameters: [$this] asc or desc, { return $PRIVATE_PROPERTY_VAR_NAME['lastName']; } with parameters: [$this] asc or desc
Starting from and up to the specified element
Index according to: { return $PUBLIC_PROPERTY_VAR_NAME['phoneNumber']; } with parameters: [$this]
Map according to: { return ['fullName' => (($PRIVATE_STATIC_PROPERTY_VAR_NAME['firstName'] . ' ') . $PUBLIC_STATIC_PROPERTY_VAR_NAME['lastName']), 'address' => $PREFIX_foo['address'], 'dateOfBirth' => $PREFIX__A__B_PRIVATE_STATIC_PROPERTY_VAR_NAME['dateOfBirth']]; } with parameters: [$this]
Get the elements as itself
ENG
        );
    }
}
