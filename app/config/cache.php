<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Cache Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache "driver" that will be used when
    | using the Caching library. Of course, you may use other drivers any
    | time you wish. This is the default when another is not specified.
    |
    | Supported: "file", "database", "apc", "memcached", "redis", "array"
    |
    */

    'driver' => 'redis',

    /*
    |--------------------------------------------------------------------------
    | File Cache Location
    |--------------------------------------------------------------------------
    |
    | When using the "file" cache driver, we need a location where the cache
    | files may be stored. A sensible default has been specified, but you
    | are free to change it to any other place on disk that you desire.
    |
    */

    'path' => storage_path().'/cache',

    /*
    |--------------------------------------------------------------------------
    | Database Cache Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" cache driver you may specify the connection
    | that should be used to store the cached items. When this option is
    | null the default database connection will be utilized for cache.
    |
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Database Cache Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" cache driver we need to know the table that
    | should be used to store the cached items. A default table name has
    | been provided but you're free to change it however you deem fit.
    |
    */

    'table' => 'cache',

    /*
    |--------------------------------------------------------------------------
    | Memcached Servers
    |--------------------------------------------------------------------------
    |
    | Now you may specify an array of your Memcached servers that should be
    | used when utilizing the Memcached cache driver. All of the servers
    | should contain a value for "host", "port", and "weight" options.
    |
    */

    'memcached' => array(

        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),

    ),

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => 'laravel',

);
