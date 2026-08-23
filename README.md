# Benchmark HTTP requests and command line calls

[![Latest Version](https://img.shields.io/packagist/v/quillstack/benchmark.svg)](https://packagist.org/packages/quillstack/benchmark)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/benchmark.svg)](https://packagist.org/packages/quillstack/benchmark)
[![PHP Version](https://img.shields.io/packagist/php-v/quillstack/benchmark)](https://packagist.org/packages/quillstack/benchmark)
[![StyleCI](https://github.styleci.io/repos/291494182/shield?branch=main)](https://github.styleci.io/repos/291494182?branch=main)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/benchmark/badge)](https://www.codefactor.io/repository/github/quillstack/benchmark)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=quillstack_benchmark&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=quillstack_benchmark)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_benchmark&metric=coverage)](https://sonarcloud.io/summary/new_code?id=quillstack_benchmark)
[![Maintainability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_benchmark&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_benchmark)
[![Reliability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_benchmark&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_benchmark)
[![Security](https://sonarcloud.io/api/project_badges/measure?project=quillstack_benchmark&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_benchmark)
[![License](https://img.shields.io/packagist/l/quillstack/benchmark)](https://github.com/quillstack/benchmark/blob/main/LICENSE)

Bash and PHP scripts to benchmark HTTP requests and command line calls. Full documentation:
https://quillstack.org/benchmark

How long does the thing take, and how many of them a second. Two shell scripts and a console
wrapper around them — `curl` and `xargs` do the work, so there is nothing to install beyond
what is already on the machine.

### Requirements

- PHP 8.1 or newer, for the console commands
- `bash`, `curl`, `awk` and `xargs` — the scripts alone need no PHP at all

The console side is [quillstack/cli](https://github.com/quillstack/cli). It used to be
Symfony's — nine packages and 1.7 MB to read three arguments, in a stack whose only other
outside dependencies are PSR interfaces.

### Installation

```shell
composer require --dev quillstack/benchmark
```

### Usage

#### HTTP requests

```shell
./vendor/bin/benchmark benchmark:http:get https://example.org 100 10
```

```text
100 requests, 10 concurrently
URL https://example.org
--------------------------------------------------------------------
Took 2.104000 s, 47.528517 requests per second, 0.196742 avg req time
```

A hundred requests, ten at a time.

#### Command line calls

The command has to print one number — the seconds it took — and nothing else:

```php
<?php

$started = microtime(true);

// … the thing being measured …

echo number_format(microtime(true) - $started, 6), "\n";
```

```shell
./vendor/bin/benchmark benchmark:console "php measured.php" 100 10
```

```text
100 calls, 10 concurrently
Command `php measured.php`
-------------------------------------------------------------------
Took 0.512000 s, 195.312500 calls per second, 0.001264 avg call time
```

#### Without PHP

The scripts run on their own:

```shell
./http_get.sh https://example.org 100 10
./command_line.sh "php measured.php" 100 10
```

### An empty measurement is worse than none

The clock is read differently on every system, and reading it wrongly used to produce this:

```text
Took  s,  requests per second, 0.067014 avg req time
```

A number nobody could see was missing. `get_milliseconds` now tries GNU `date`, then `perl`,
then `python3`, and refuses outright where none of them can answer. A count which is not a
positive whole number is refused too, rather than reaching a shell as part of a command.

### Technical documentation

| File | What it is |
| --- | --- |
| `http_get.sh` | asks a URL many times, some at once, and reports |
| `command_line.sh` | runs a command many times and reports |
| `lib/common.sh` | the clock, and the check that a count is one |
| `src/Benchmark.php` | finds the scripts and runs them with everything escaped |
| `src/Commands/HttpGetBenchmarkCommand.php` | `benchmark:http:get` |
| `src/Commands/ConsoleBenchmarkCommand.php` | `benchmark:console` |

Both take the same three arguments: what to measure, how many times, and how many at once.

Everything reaching a shell goes through `escapeshellarg()`, so a URL holding a quote is a URL
rather than a second command.

### Static analysis

```shell
composer stan
```

### License

MIT. See [LICENSE](LICENSE).
