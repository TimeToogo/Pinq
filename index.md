---
layout: default
title:  Home
---
                
What is PINQ?
=============
Based off the .NET's [LINQ (Language integrated query)](http://msdn.microsoft.com/en-us/library/bb397926.aspx),
PINQ unifies querying across [arrays/iterators](examples.html) and [external data sources](query-provider.html),
in a single readable and concise [fluent API](api.html).

A quick example
===============

The following shows an example query using the PINQ library:

{% highlight php startinline %}

$youngPeopleDetails = $people
        ->where(function ($row) { return $row['age'] <= 50; })
        ->orderByAscending(function ($row) { return $row['firstName']; })
        ->thenByAscending(function ($row) { return $row['lastName']; })
        ->take(50)
        ->indexBy(function ($row) { return $row['phoneNumber']; })
        ->select(function ($row) { 
            return [
                'fullName'    => $row['firstName'] . ' ' . $row['lastName'],
                'address'     => $row['address'],
                'dateOfBirth' => $row['dateOfBirth'],
            ]; 
        })

{% endhighlight %}

The beauty of PINQ is as follows, the above query may be executing against a set of arrays or
possibly against database table. PINQ blurs the lines between in-memory and external data-source,
using the most powerful and natural language available to PHP developers: ... PHP.

Why PINQ?
=========

Considering the <a href="https://github.com/search?q=php+linq&type=Repositories&ref=searchresults" target="_blank">vast number of LINQ implementations</a>
currently available for php, why another?
Besides the fact most of the others are incomplete/broken without a maintainer 
nor contributors, they are not real LINQ implementations. Sure, offering some nice array handling
syntax is cool, but not only what LINQ is about. PINQ aims to provide:

 - Well structured and tested code base
 - Full composer and PSR-4 support
 - Complete and thorough documentation
 - LINQ-style fluent query API with lazy evaluation and immutable query objects
 - Truly seamless external query support
 - Full support for PHP closures and callable syntax (No magic strings)
 - And even building on the original LINQ with offering an additional mutable query API

Installation
============
Add the package to your composer.json:

```json
{
    "require": {
        "timetoogo/pinq": "~3.0"
    }
}
```