---
layout: default
title:  Examples
---
Examples using Traversable
==========================

Here are some examples demonstrating the functionality of the query API:

**Starting off with the values**

{% highlight php startinline %}
use Pinq\ITraversable, Pinq\Traversable;

$Numbers = Traversable::From(range(1, 100));
$Strings = Traversable::From(['foo', 'bar', 'baz', 'tear', 'cow', 'tripod', 'whisky', 'sand', 'which']);
{% endhighlight %}

**Filtering**

{% highlight php startinline %}
foreach($Numbers->Where(function ($I) { return $I % 2 === 0; }) as $Number) {
    //2, 4, 6, 8...
}

foreach($Numbers->Where(function ($I) { return $I % 2 === 1; }) as $Number) {
    //1, 3, 5, 7....
}

foreach($Strings->Where(function ($I) { return strpos($I, 'w') === 0; })) as $String) {
    //'whiskey', 'which'
}
{% endhighlight %}

**Ordering**

{% highlight php startinline %}

foreach($Numbers->OrderByAscending(function ($I) { return -$I; }) as $Number) {
    //100, 99, 98, 97, 96...
}

foreach($Strings
        ->OrderByAscending(function ($I) { return $I[0]; })
        ->ThenByDescending(function ($I) { return $I[2]; }) as $String) {
    //'baz', 'bar', 'cow', 'foo'...
}
{% endhighlight %}

**Grouping**

{% highlight php startinline %}

foreach($Numbers->GroupBy(function ($I) { return $I % 2; }) as $Group) {
    //Traversable: [1, 3, 5, 7...], Traversable: [2, 4, 6, 8...]
}

foreach($Strings->GroupBy(function ($I) { return $I[0]; }) as $String) {
    //Traversable: ['foo'], Traversable: ['bar', 'baz'], Traversable: ['tear', 'tripod']...
}

{% endhighlight %}

**Selecting**

{% highlight php startinline %}

foreach($Numbers->Select(function ($I) { return $I * 10; }) as $Number) {
    //10, 20, 30, 40...
}

foreach($Strings->Select(function ($I) { return $I . '-poo'; }) as $String) {
    //'foo-poo', 'bar-poo', 'baz-poo', 'tear-poo'...
}

foreach($Strings->Select('strlen') as $Length) {
    //3, 3, 3, 4...
}

{% endhighlight %}

**Selecting many**

{% highlight php startinline %}

foreach($Strings->SelectMany('str_split') as $Character) {
    //'f', 'o', 'o', 'b', 'a'...
}

{% endhighlight %}

**Aggregating**

{% highlight php startinline %}

$Numbers->Aggregate(function ($I, $K) { return $I * $K }); //100! (1 * 2 * 3 * 4...)

$Numbers->Count(); //100

$Numbers->Exists(); //true

$Numbers->Sum(); //5050 (1 + 2 + 3 + 4...)

$Numbers->Average(); //50.5

$Numbers->Maximum(); //100

$Numbers->Implode('-'); //'1-2-3-4-5-6...'


$Strings->Implode(''); //'foobarbaztear...'

$Strings->All(function ($I) { return strlen($I) >= 3; }); //true

$Strings->Any(function ($I) { return strpos($I, 'z') !== false; }); //false

$Strings->Average('strlen'); //4.111...

{% endhighlight %}

**Bringing it together**

{% highlight php startinline %}

$NumberData = $Numbers
        ->Where(function ($I) { return $I % 2 === 0; }) //Only even values
        ->OrderByDescending(function ($I) { return $I; }) //Order from largest to smallest
        ->GroupBy(function ($I) { return $I % 7; }) //Put into seven groups
        ->Where(function (ITraversable $I) { return $I->Count() % 2 === 0; }) //Only groups with an even amount of values
        ->Select(function (ITraversable $Numbers) {
            return [
                'First' => $Numbers->First(),
                'Average' => $Numbers->Average(),
                'Count' => $Numbers->Count(),
                'Numbers' => $Numbers->AsArray(),
            ];
        });

{% endhighlight %}
