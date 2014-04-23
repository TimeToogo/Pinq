---
layout: default
title:  Performance
---
Performance
===========

When it comes to performance, there are two distinct sections when using Pinq.

In memory queries
=================

When utilising either the `Traversable` or `Collection` performing
queries in memory, there should be little overhead from these operations. Obviously,
if the data set was absurdly large, you could run into issues. But for most uses cases,
you may actually incur a performance benefit, due to [lazy evaluation](details.html) and 
efficient iterator use. 

External queries
================

For either `Queryable` or `Repository`, there is cause for concern. When
querying these implementations, to accurately interperate them, the supplied functions 
will parsed into [expression trees](expressions.html). This is a very expensive process and
can incur a significant performance penalties if not setup correctly. 

Caching parsed functions
========================

To combat this, there are several options provided. Configuring the `Pinq\Caching\Provider` class to
set up the default caching mechanisms:

 - **File Cache** - Caches the data in a single file, this is not optimal for high concurrency 
   but is suited to those without access to a proper memory cache.

   {% highlight php startinline %}\Pinq\Caching\Provider::setFileCache($FilePath);{% endhighlight %}

 - **Directory Cache** - Caches the data in multiple files of a directory, while this may be
   relatively better for concurrency, is not very performant and a proper memory cache should be prefered.

   {% highlight php startinline %}\Pinq\Caching\Provider::setDirectoryCache($DirectoryPath);{% endhighlight %}

 - **Doctrine Cache** - This allows you to use any cache from the `Doctrin\Cache` component,

   {% highlight php startinline %}\Pinq\Caching\Provider::setDoctrineCache($CacheImplementation);{% endhighlight %}

 - **Array Access Cache** - This allows you to use any cache that implements `ArrayAccess`

   {% highlight php startinline %}\Pinq\Caching\Provider::setArrayAccessCache($CacheImplementation);{% endhighlight %}

 - **Custom Cache** - Allows any cache implementing `Pinq\Caching\IFunctionCache`

   {% highlight php startinline %}\Pinq\Caching\Provider::setCustomCache($CacheImplementation);{% endhighlight %}

All of the above options will significantly reduce the performance penalties by removing
the need to parse the functions on every request. A proper memory cache such as 
[Memcache](http://memcached.org/) or [Redis](http://redis.io/) using the `Doctrine\Cache` component 
should be preferred but the alternative file/directory cache should be sufficient 
for small sites and only requires disk access rather than the hassle of setting up a real cache.

During Development
==================

Using a cache during development can be problematic as when queries are changed, they may be ignored
and continue to use the original version. So during the development phase you should use:

{% highlight php startinline %}
\Pinq\Caching\Provider::setDevelopmentMode(true);
{% endhighlight %}

When this flag it set to true, the cache will be cleared upon every request. But please remember to
disable this before going into production.