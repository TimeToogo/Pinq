PHP Integrated Query - [Official site](http://timetoogo.github.io/Pinq/)
========================================================================

[![Build status](https://img.shields.io/travis/TimeToogo/Pinq/master.svg?style=flat-square)](https://travis-ci.org/TimeToogo/Pinq)
[![Code quality](https://img.shields.io/scrutinizer/g/TimeToogo/Pinq.svg?style=flat-square)](https://scrutinizer-ci.com/g/TimeToogo/Pinq)
[![Coverage Status](https://img.shields.io/coveralls/TimeToogo/Pinq/master.svg?style=flat-square)](https://coveralls.io/r/TimeToogo/Pinq?branch=master)
[![Stable Release](https://img.shields.io/packagist/v/TimeToogo/Pinq.svg?style=flat-square)](https://packagist.org/packages/timetoogo/pinq)
[![License](https://img.shields.io/github/license/TimeToogo/Pinq.svg?style=flat-square)](https://packagist.org/packages/timetoogo/pinq)

What is PINQ?
=============

Based off the .NET's [LINQ (Language integrated query)](http://msdn.microsoft.com/en-us/library/bb397926.aspx), 
PINQ unifies querying across [arrays/iterators](http://timetoogo.github.io/Pinq/examples.html) and [external data sources](http://timetoogo.github.io/Pinq/query-provider.html),
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
                'fullName'    => $row['firstName'] . ' ' . $row['lastName'],
                'address'     => $row['address'],
                'dateOfBirth' => $row['dateOfBirth'],
            ]; 
        });
```

[More examples](http://timetoogo.github.io/Pinq/examples.html)

Installation
============

PINQ is compatible with >= PHP 7.3

Install the package via composer:

```
composer require timetoogo/pinq
```

