---
layout: default
title:  Details
---

Lazy Evaluation 
==============

Like its cousin, Linq, Pinq makes extensive use of **lazy evaluation**:

{% highlight php startinline %}
$FilteredValues = $Values->Where(function ($I) { return strlen($I) < 50; });
{% endhighlight %}

In the above example, the supplied function would not be executed until the values are actually
required:

{% highlight php startinline %}
foreach($FilteredValues as $Value) {
    //The function would begin executing as the values are iterated
}
{% endhighlight %}

Immutability
============

Pinq queries are also **immutable**, that is:

{% highlight php startinline %}

$Values = \Pinq\Traversable::From(range(1, 10));

$Values->Where(function ($I) { return $I >= 5; });

foreach($Values as $Value) {
    //1, 2, 3, 4, 5, 6....
}

{% endhighlight %}

You might ask why it is this way, the reason being is demonstrated below:

{% highlight php startinline %}

$Values = \Pinq\Traversable::From(range(1, 10));


foreach($Values->Where(function ($I) { return $I >= 5; }) as $Value) {
    //5, 6, 7, 8....
}

foreach($Values->Where(function ($I) { return $I < 5; }) as $Value) {
    /* 
     * If the queries mutated the original object,
     * no values would be iterated here, this would be very unintuitive and bug-prone
     */
}

{% endhighlight %}

The correct way to write the original query would be as follows:

{% highlight php startinline %}

$Values = \Pinq\Traversable::From(range(1, 10));

//Override the original with the filtered values
$Values = $Values->Where(function ($I) { return $I >= 5; });

foreach($Values as $Value) {
    //5, 6, 7, 8....
}

{% endhighlight %}

The other side
==============

This may get a bit confusing but bear with me, all the queries specific to 
`ICollection`/`IRepository` are evaluated **eagerly** and **do mutate** the original object. 

**Firstly, the mutating:**

{% highlight php startinline %}

$Values = \Pinq\Collection::From(range(1, 10));

$Values->RemoveRange(range(1, 4));

foreach($Values as $Value) {
    //5, 6, 7, 8...
}

{% endhighlight %}

The `ICollection` is designed to offer the additional mutability aspect to the `ITraversable`

One could easily write the same using just the `ITraversable`:

{% highlight php startinline %}
$Values = \Pinq\Traversable::From(range(1, 10));

$Values = $Values->Except(range(1, 4));

foreach($Values as $Value) {
    //5, 6, 7, 8...
}
{% endhighlight %}

This may seem better at first, you even get the lazy evaluation,
but the difference becomes apparent when using external data sources.
What if you were querying an underlying database and you actually wanted
to remove the values? This is why an additional `ICollection`/`IRepository` 
is needed and implemented the way they are.

Mutability is something that needs to be catered for. 

**Secondly, the eager evaluation:**

{% highlight php startinline %}

$Values = \Pinq\Collection::From(range(1, 10));

$Values->Apply(function (&$Number) { $Number *= 10; });

{% endhighlight %}

Why should this be evaluated eagerly? It clearly would be nicer if it didn't 
have to execute against the values right away and also be evaluated upon iteration.
Well, no, back to the same point with external data sources, if you happend to call:

{% highlight php startinline %}
$Values->Apply(function (&$Number) { $Number *= 10; });
{% endhighlight %}

And the `$Values` was not an `ICollection` with an underlying array array but instead 
a `IRepository` querying a flatfile. But since the values were never iterated, the query
 would never have executed and your flatfile data would remain untouched. This is clearly 
the wrong path to go down. If this is the behaviour you are seeking, you should be using `Select`. 

That is why the queries specific to `ICollection`/`IRepository` are evaluated **eagerly**.
