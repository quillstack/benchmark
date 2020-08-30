# Benchmark HTTP requests and command line calls

This repository contains a script to test HTTP GET requests or command line calls.

### HTTP GET requests
Usage:

```
./http_get.sh https://example.org 10 2
```

Where:
- 10 is a total number of requests
- 2 is a number of concurrent requests

Output:

```
10 requests, 2 concurrently
URL https://example.org
--------------------------------------------------------------------
Took 2.468000 s, 4.051864 requests per second, 0.469415 avg req time
```

What means we sent 10 GET requests to this host. We decided to send two
requests at the time. The entire test took around 2.5 seconds. It means
the website could server 4 requests per second with 0.5 seconds the average
response time.

### Command line calls

If you want to test command line calls, be sure every call respond with
an execution time (in seconds):

```
0.001699
``` 

For PHP the script could look like:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

$start = microtime(true);

$container = new Container();
$service = $container->get(ExampleService::class);

$time = microtime(true) - $start;
$roundedTime = round($time, 6);

echo $roundedTime . PHP_EOL;
```

Usage example:

```
./command_line.sh "php ../dependency-injection/public/index.php" 10 2
```

Output:

```
10 calls, 2 concurrently
Command `php ../dependency-injection/public/index.php`
-------------------------------------------------------------------
Took 0.247000 s, 40.485830 calls per second, 0.001087 avg call time
```

The results say we called the script 10 times with 2 concurrently calls.
Our test took around 250 milliseconds, what means we could call this
script 40 times per seconds, and average call time would be 1 millisecond.

#### Different results

You can have different results for different parameters. For example the
results for the same script:

```
./command_line.sh "php ../dependency-injection/public/index.php" 1000 100
```

are different for these parameters:

```
1000 calls, 100 concurrently
Command `php ../dependency-injection/public/index.php`
-------------------------------------------------------------------
Took 7.760000 s, 128.865979 calls per second, 0.080927 avg call time
```

Because we increased the number of concurrent calls.

If we use the same concurrent calls like in the first command line example:

```
./command_line.sh "php ../dependency-injection/public/index.php" 1000 2
```

The results should be similar to the first ones:

```
1000 calls, 2 concurrently
Command `php ../dependency-injection/public/index.php`
-------------------------------------------------------------------
Took 22.795000 s, 43.869270 calls per second, 0.001178 avg call time
```

What gives use around 40 calls per second, if we have two concurrent
calls.
