---
layout: default
title:  Home
---
                
What is Pinq?
=============
Based off the .NET's [Linq (Language integrated query)](http://msdn.microsoft.com/en-us/library/bb397926.aspx), 
Pinq unifies querying across [arrays/iterators](examples.html) and [external data sources](query-provider.html), 
in a single readable and concise [fluent API](api.html).

A quick example
===============

The following shows an example query using the Pinq library:

{% highlight php startinline %}

$YoungPeopleDetails = $People
        ->Where(function ($Row) { return $Row['Age'] <= 50; })
        ->OrderByAscending(function ($Row) { return $Row['FirstName']; })
        ->ThenByAscending(function ($Row) { return $Row['LastName']; })
        ->Take(50)
        ->IndexBy(function ($Row) { return $Row['PhoneNumber']; })
        ->Select(function ($Row) { 
            return [
                'FullName' => $Row['FirstName'] . ' ' . $Row['LastName'],
                'Address' => $Row['Address'],
                'DateOfBirth' => $Row['DateOfBirth'],
            ]; 
        })

{% endhighlight %}

The beauty of Pinq is as follows, the above query may be executing against a set of arrays or
possibly against database table. Pinq blurs the lines between in-memory and external data-source,
using the to most powerful and natural language available to PHP developers: ... PHP.

Why Pinq?
=========

Considering the <a href="https://github.com/search?q=php+linq&type=Repositories&ref=searchresults" target="_blank">vast number of Linq implementations</a> 
currently available for php, why another?
Besides the fact most of the others are incomplete/broken without a maintainer 
nor contributors, they are not real Linq implementations. Sure, offering some nice array handling 
syntax is cool, but not only what Linq is about. Pinq aims to provide:

 - Well structured and tested code base
 - Full composer and PSR-4 support
 - Complete and thorough documentation
 - Linq-style fluent query API with lazy evaluation and immutable query objects
 - Truly seamless external query support
 - Full support for PHP closures and callable syntax (No magic strings)
 - And even building on the original Linq with offering an additional mutable query API


Installation
============
Add the package to your composer.json:

```json
{
    "require": {
        "timetoogo/pinq": "dev-master"
    }
}
```