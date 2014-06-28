PHP Integrated query - [Official site](http://timetoogo.github.io/Pinq/)
========================================================================
[![Build status](https://api.travis-ci.org/TimeToogo/Pinq.png)](https://travis-ci.org/TimeToogo/Pinq)
[![Code quality](https://scrutinizer-ci.com/g/TimeToogo/Pinq/badges/quality-score.png?s=ddce8f86d3192ab4ca1134aa98e17ab7340014f7)](https://scrutinizer-ci.com/g/TimeToogo/Pinq)
[![Coverage Status](https://coveralls.io/repos/TimeToogo/Pinq/badge.png?branch=master)](https://coveralls.io/r/TimeToogo/Pinq?branch=master)
[![Stable Release](https://poser.pugx.org/timetoogo/pinq/v/stable.png)](https://packagist.org/packages/timetoogo/pinq)
[![License](https://poser.pugx.org/timetoogo/pinq/license.png)](https://packagist.org/packages/timetoogo/pinq)

What is Pinq?
=============
Based off the .NET's [Linq (Language integrated query)](http://msdn.microsoft.com/en-us/library/bb397926.aspx), 
Pinq unifies querying across [arrays/iterators](http://timetoogo.github.io/Pinq/examples.html) and [external data sources](http://timetoogo.github.io/Pinq/query-provider.html), 
in a single readable and concise [fluent API](http://timetoogo.github.io/Pinq/api.html).

An example
==========

```php
$youngPeopleDetails = $people
        ->where(function ($row) { return $row['age'] <= 50; })
        ->orderByAscending(function ($row) { return $row['firstName']; })
        ->thenByAscending(function ($row) { return $row['lastName']; })
        ->take(50)
        ->indexBy(function ($row) { return $row['phoneNumber']; })
        ->select(function ($row) { 
            return [
                'fullName' => $row['firstName'] . ' ' . $row['lastName'],
                'address' => $row['address'],
                'dateOfBirth' => $row['dateOfBirth'],
            ]; 
        });
```


Installation
============
Add package to your composer.json:
```json
{
    "require": {
        "timetoogo/pinq": "~2.1"
    }
}
```

