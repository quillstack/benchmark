# Benchmark HTTP requests and command line calls

This repository contains a script to test HTTP GET requests or command line calls.

## PHP usage

You can install this package using _Composer_:

```
composer require --dev quillstack/benchmark
```

In PHP console you can use this library by running commands:

##### List available commands

The first line is available when you cloned this repository to your
local computer. The second line is ready to use if you installed this
library as a package in your project:

```
php bin/local.php
php vendor/quillstack/benchmark/bin/benchmark.php
```

##### HTTP GET requests

```
php bin/local.php benchmark:http:get https://example.org 10 2
php vendor/quillstack/benchmark/bin/benchmark.php benchmark:http:get https://example.org 10 2
```

##### Command line calls

```
php bin/local.php benchmark:console "php ../dependency-injection-example/public/php-di.php" 10 2
php vendor/quillstack/benchmark/bin/benchmark.php benchmark:console "php ../dependency-injection-example/public/php-di.php" 10 2
```

To see detailed descriptions for every command, ready Bash usage below.

## Bash usage

You can also use Bash commands to run benchmarks.

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
./command_line.sh "php ../di/public/index.php" 10 2
```

Output:

```
10 calls, 2 concurrently
Command `php ../di/public/index.php`
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
./command_line.sh "php ../di/public/index.php" 1000 100
```

are different for these parameters:

```
1000 calls, 100 concurrently
Command `php ../di/public/index.php`
-------------------------------------------------------------------
Took 7.760000 s, 128.865979 calls per second, 0.080927 avg call time
```

Because we increased the number of concurrent calls.

If we use the same concurrent calls like in the first command line example:

```
./command_line.sh "php ../di/public/index.php" 1000 2
```

The results should be similar to the first ones:

```
1000 calls, 2 concurrently
Command `php ../di/public/index.php`
-------------------------------------------------------------------
Took 22.795000 s, 43.869270 calls per second, 0.001178 avg call time
```

What gives use around 40 calls per second, if we have two concurrent
calls.

## Quill Stack

If you want to know more about other solutions, visit the website: \
https://quillstack.com/ 

![The Quill Stack](https://raw.githubusercontent.com/quillstack/quillstack.com/master/docs/quillstack.png)
