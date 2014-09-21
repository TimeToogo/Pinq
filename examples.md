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

$numbers = Traversable::from(range(1, 100));
$strings = Traversable::from(['foo', 'bar', 'baz', 'tear', 'cow', 'tripod', 'whisky', 'sand', 'which']);
{% endhighlight %}

**Filtering**

{% highlight php startinline %}
foreach($numbers->where(function ($i) { return $i % 2 === 0; }) as $number) {
    //2, 4, 6, 8...
}

foreach($numbers->where(function ($i) { return $i % 2 === 1; }) as $number) {
    //1, 3, 5, 7....
}

foreach($strings->where(function ($i) { return strpos($i, 'w') === 0; })) as $string) {
    //'whiskey', 'which'
}

foreach($strings->where(function ($i, $key) { return $key % 2 === 0; })) as $string) {
    //'foo', 'baz', 'cow'
}
{% endhighlight %}

**Ordering**

{% highlight php startinline %}

foreach($numbers->orderByAscending(function ($i) { return -$i; }) as $number) {
    //100, 99, 98, 97, 96...
}

foreach($strings
        ->orderByAscending(function ($i) { return $i[0]; })
        ->thenByDescending(function ($i) { return $i[2]; }) as $string) {
    //'baz', 'bar', 'cow', 'foo'...
}
{% endhighlight %}

**Grouping**

{% highlight php startinline %}

foreach($numbers->groupBy(function ($i) { return $i % 2; }) as $group) {
    //Traversable: [1, 3, 5, 7...], Traversable: [2, 4, 6, 8...]
}

foreach($strings->groupBy(function ($i) { return $i[0]; }) as $string) {
    //Traversable: ['foo'], Traversable: ['bar', 'baz'], Traversable: ['tear', 'tripod']...
}

{% endhighlight %}

**Joining**

{% highlight php startinline %}

foreach($numbers
        ->join(range(10, 20))
        ->onEquality(function ($outer) { return $outer * 2; }, function ($inner) { return $inner; })
        ->to(function($outer, $inner) { return $outer . ':' . $inner; }) as $joined) {
    //'5:10', '6:12', '7:14', '8:16', '9:18', '10:20'
}

foreach($numbers
        ->groupJoin($strings)
        ->onEquality(function ($outer) { return $outer; }, 'strlen')
        ->to(function($outer, ITraversable $innerGroup) { return $outer . ':' . $innerGroup->implode('|'); }) as $joined) {
    //'1:', '2:', '3:foo|bar|baz|cow', '4:tear|sand', '5:which', '6:tripod|whisky', '7:'...
}

foreach($numbers
        ->groupJoin($numbers)
        ->on(function ($outer, $inner) { return $outer >= $inner; })
        ->to(function($outer, ITraversable $innerGroup) { return $outer . ':' . $innerGroup->implode('|'); }) as $joined) {
    //'1:1', '2:1|2', '3:1|2|3', '4:1|2|3|4', '5:1|2|3|4|5'...
}

{% endhighlight %}

**Selecting**

{% highlight php startinline %}

foreach($numbers->select(function ($i) { return $i * 10; }) as $number) {
    //10, 20, 30, 40...
}

foreach($strings->select(function ($i) { return $i . '-poo'; }) as $string) {
    //'foo-poo', 'bar-poo', 'baz-poo', 'tear-poo'...
}

foreach($strings->select('strlen') as $length) {
    //3, 3, 3, 4...
}

{% endhighlight %}

**Selecting many**

{% highlight php startinline %}

foreach($strings->selectMany(function ($i) { return str_split($i); }) as $character) {
    //'f', 'o', 'o', 'b', 'a'...
}

{% endhighlight %}

**Aggregating**

{% highlight php startinline %}

$numbers->aggregate(function ($i, $k) { return $i * $k }); //100! (1 * 2 * 3 * 4...)

$numbers->count(); //100

$numbers->exists(); //true

$numbers->sum(); //5050 (1 + 2 + 3 + 4...)

$numbers->average(); //50.5

$numbers->maximum(); //100

$numbers->implode('-'); //'1-2-3-4-5-6...'


$strings->implode(''); //'foobarbaztear...'

$strings->all(function ($i) { return strlen($i) >= 3; }); //true

$strings->any(function ($i) { return strpos($i, 'z') !== false; }); //false

$strings->average('strlen'); //4.111...

{% endhighlight %}

**Bringing it together**

{% highlight php startinline %}

$numberData = $numbers
        ->where(function ($i) { return $i % 2 === 0; }) //Only even values
        ->orderByDescending(function ($i) { return $i; }) //Order from largest to smallest
        ->groupBy(function ($i) { return $i % 7; }) //Put into seven groups
        ->where(function (ITraversable $i) { return $i->count() % 2 === 0; }) //Only groups with an even amount of values
        ->select(function (ITraversable $numbers, $isEven) {
            return [
                'isEven'     => $isEven,
                'average'  => $numbers->average(),
                'count'      => $numbers->count(),
                'numbers' => $numbers->asArray(),
            ];
        });

{% endhighlight %}
