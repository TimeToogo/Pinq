---
layout: default
title:  Home
---
                
What is Pinq?
=============
Based off the .NET's [Linq](http://msdn.microsoft.com/en-us/library/bb397926.aspx), 
Pinq unifies querying across [arrays/iterators](examples.html) and [external data sources](query-provider.html), 
in a single readable and concise [fluent API](api.html).

A quick example
===============

The following shows an example query using the Pinq library:

```php

$YoungPeopleDetails = $People
        ->Where(function (array $Row) { return $Row['Age'] <= 50; })
        ->OrderByAscending(function (array $Row) { return $Row['FirstName']; })
        ->ThenByAscending(function (array $Row) { return $Row['LastName']; })
        ->Take(50)
        ->IndexBy(function (array $Row) { return $Row['PhoneNumber']; })
        ->Select(function (array $Row) { 
            return [
                'FullName' => $Row['FirstName'] . ' ' . $Row['LastName'],
                'Address' => $Row['Address'],
                'DateOfBirth' => $Row['DateOfBirth'],
            ]; 
        })

```

The beauty of Pinq is as follows, the above query may be executing against a set of arrays or
possibly against database table. Pinq blurs the lines between in-memory and external data-source,
using the to most powerful and natural language available to PHP developers: ... PHP.

Installation
============
Add package to your composer.json:

```json
{
    "require": {
        "timetoogo/pinq": "dev-master"
    }
}
```