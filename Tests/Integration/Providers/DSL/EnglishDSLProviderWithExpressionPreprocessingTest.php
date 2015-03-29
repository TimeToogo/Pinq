<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors\VariablePrefixerProcessor;

class EnglishDSLProviderWithExpressionPreprocessingTest extends EnglishDSLProviderTest
{
    protected function preprocessorFactories()
    {
        return [VariablePrefixerProcessor::factory('__')];
    }

    protected function aggregateQuery()
    {
        return <<<'ENG'
Get the values aggregated according to the function: { return ($__a + $__s); } with parameters: [$__this]
ENG;
    }

    protected function applyQuery()
    {
        return <<<'ENG'
Update the values according to the function: { $__i *= 10; } with parameters: [$__this]
ENG;
    }


    protected function addSubscopeQuery()
    {
        return <<<'ENG'
Add the following values: [
    Filter according to: { return ($__i > 5); } with parameters: [$__this]
]
ENG;
    }

    protected function removeSubscopeQuery()
    {
        return <<<'ENG'
Remove the following values: [
    Filter according to: { return ($__i < 3); } with parameters: [$__this]
]
ENG;
    }

    protected function removeWhereQuery()
    {
        return <<<'ENG'
Remove the elements according to: { return true; } with parameters: [$__this]
ENG;
    }

    protected function joinApplyQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] filtered according to: { return true; } with parameters: [$__this] and update the outer values according to: { $__o *= $__i; } with parameters: [$__this]
ENG;
    }

    protected function whereQuery()
    {
        return <<<'ENG'
Filter according to: { return true; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function orderByAscendingQuery()
    {
        return <<<'ENG'
Order according to: { return $__i; } with parameters: [$__this] asc or desc
Get the elements as itself
ENG;
    }

    protected function orderByDescendingQuery()
    {
        return <<<'ENG'
Order according to: { return $__i; } with parameters: [$__this] asc or desc
Get the elements as itself
ENG;
    }

    protected function orderByAscendingWithThenByAscendingQuery()
    {
        return <<<'ENG'
Order according to: { return $__i; } with parameters: [$__this] asc or desc, { return $__i; } with parameters: [$__this] asc or desc
Get the elements as itself
ENG;
    }

    protected function orderByAscendingWithThenByDescendingQuery()
    {
        return <<<'ENG'
Order according to: { return $__i; } with parameters: [$__this] asc or desc, { return $__i; } with parameters: [$__this] asc or desc
Get the elements as itself
ENG;
    }

    protected function indexByQuery()
    {
        return <<<'ENG'
Index according to: { return $__i; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function groupByQuery()
    {
        return <<<'ENG'
Group according to: { return $__i; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function unfilteredJoinQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function joinOnQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] filtered according to: { return ($__o === $__i); } with parameters: [$__this] and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function joinOnEqualityQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] filtered according to: { return $__o; } with parameters: [$__this] equaling { return $__i; } with parameters: [$__this] and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function joinWithDefaultQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] with default values and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function joinTwoWithDefaultsQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] with default values and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function joinToSubScopeQuery()
    {
        return <<<'ENG'
Join with: [
    Order according to: { return $__i; } with parameters: [$__this] asc or desc
    Map according to: { return $__i; } with parameters: [$__this]
    Starting from and up to the specified element
] and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function unfilteredGroupJoinWithDefaultQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] into groups and correlate the values according to: { return [$__o, $__i]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function selectQuery()
    {
        return <<<'ENG'
Map according to: { return $__i; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function selectManyQuery()
    {
        return <<<'ENG'
Map and flatten according to: { return $__i; } with parameters: [$__this]
Get the elements as itself
ENG;
    }

    protected function intersectWithFilteredSubScopeQuery()
    {
        return <<<'ENG'
The intersection with: [
    Filter according to: { return true; } with parameters: [$__this]
]
Get the elements as itself
ENG;
    }

    protected function exampleFromDocsQuery()
    {
        return <<<'ENG'
Filter according to: { return ($__row['age'] <= 50); } with parameters: [$__this]
Order according to: { return $__row['firstName']; } with parameters: [$__this] asc or desc, { return $__row['lastName']; } with parameters: [$__this] asc or desc
Starting from and up to the specified element
Index according to: { return $__row['phoneNumber']; } with parameters: [$__this]
Map according to: { return ['fullName' => (($__row['firstName'] . ' ') . $__row['lastName']), 'address' => $__row['address'], 'dateOfBirth' => $__row['dateOfBirth']]; } with parameters: [$__this]
Get the elements as itself
ENG;
    }
}
