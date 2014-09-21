---
layout: default
title:  Performance
---
Performance
===========

When it comes to performance, there are two distinct sections when using PINQ.

In memory queries
=================

When utilising either the `Traversable` or `Collection` the queries are performed
in memory, there should be little overhead from these operations. Obviously,
if the data set was absurdly large, you could run into issues. But for most uses cases,
you may actually incur a performance benefit, due to [lazy evaluation](details.html) and 
efficient iterator use. 

External queries
================

For either `Queryable` or `Repository`, there is cause for concern. When
querying these implementations, to accurately interpret them, the supplied functions
will parsed into [expression trees](queries-and-expressions.html). This is a very expensive process and
can incur a significant performance penalties if not setup correctly. 

Caching parsed queries
======================

To combat this, there are several options provided. Configuring the `Pinq\Caching\CacheProvider` class to
set up the default caching mechanisms:

 - **File Cache** - Caches the data in a single file, this is not optimal for high concurrency 
   but is suited to those without access to a proper memory cache.

   {% highlight php startinline %}\Pinq\Caching\Provider::setFileCache($filePath);{% endhighlight %}

 - **Directory Cache** - Caches the data in multiple files of a directory, while this may be
   relatively better for concurrency, is not very performant and a proper memory cache should be prefered.

   {% highlight php startinline %}\Pinq\Caching\Provider::setDirectoryCache($directoryPath);{% endhighlight %}

 - **Doctrine Cache** - This allows you to use any cache from the `Doctrine\Cache` component,

   {% highlight php startinline %}\Pinq\Caching\Provider::setDoctrineCache($cacheImplementation);{% endhighlight %}

 - **Array Access Cache** - This allows you to use any cache that implements `ArrayAccess`

   {% highlight php startinline %}\Pinq\Caching\Provider::setArrayAccessCache($cacheImplementation);{% endhighlight %}

 - **Custom Cache** - Allows any cache implementing `Pinq\Caching\ICacheAdapter`

   {% highlight php startinline %}\Pinq\Caching\Provider::setCustomCache($cacheImplementation);{% endhighlight %}

All of the above options will significantly reduce the performance penalties by removing
the need to parse the queries on every request. A proper memory cache such as
[Memcache](http://memcached.org/) or [Redis](http://redis.io/) using the `Doctrine\Cache` component 
should be preferred but the alternative file/directory cache should be sufficient 
for small applications and only requires disk access rather than the hassle of setting up a real cache.

During Development
==================

Using a cache during development can be problematic as when queries are changed, they may be ignored
and continue to use the original version. So during the development phase you should use:

{% highlight php startinline %}
\Pinq\Caching\CacheProvider::setDevelopmentMode(true);
{% endhighlight %}

When this flag it set to true, the cache will be cleared upon every request. But please remember to
disable this before going into production.