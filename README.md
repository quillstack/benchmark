# Benchmark HTTP requests and command line calls

[![Tests](https://github.com/quillstack/benchmark/actions/workflows/tests.yml/badge.svg)](https://github.com/quillstack/benchmark/actions/workflows/tests.yml)
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

## Why this exists

Every benchmark in this framework's documentation was produced with this, which is the reason it
had to be trustworthy before any of them could be.

It measures two things: how long an HTTP endpoint takes under some concurrency, and how long a
command takes. `curl`, `xargs` and `awk` do the work, so there is nothing to install beyond what
is on the machine — and the shell scripts run without PHP at all.

**An empty measurement is worse than none**, which this package learned the hard way: it once
reported timings of zero because it shelled out to a `python` that was not there, and nothing
said so. It refuses now rather than printing a number nobody can trust.

## Requirements

- PHP 8.1 or newer, for the console commands
- `bash`, `curl`, `awk` and `xargs` — the scripts alone need no PHP at all

The console side is [quillstack/cli](https://github.com/quillstack/cli). It used to be
Symfony's — nine packages and 1.7 MB to read three arguments, in a stack whose only other
outside dependencies are PSR interfaces.

## Installation

```shell
composer require --dev quillstack/benchmark
```

## Usage

### HTTP requests

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

### Command line calls

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

### Without PHP

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

## Technical documentation

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

## Benchmark

**There is one other tool and it does something else.**

[phpbench](https://github.com/phpbench/phpbench) is the benchmarking framework for PHP, and it
is a microbenchmark harness: it runs a method thousands of times inside one process, controls
warm-up and revolutions, does statistics on the distribution, stores history and detects
regressions between runs. It is the right tool for asking whether a function got slower.

This asks a different question — how long does *this whole command* take, or *this endpoint*
under ten at once — by running the thing as a process and timing it from outside. That is why
the figures in this framework's READMEs are medians of interleaved runs rather than
distributions: the tool measures a whole process, and the shape of a hundred process launches is
not a distribution worth doing statistics on.

Timing the two against each other would produce a number that means nothing, because they are
not measuring the same thing. **If you want to know whether a method regressed, use phpbench.**

## Tests

```shell
composer test
```

They cover the decisions this package makes before a measurement is taken: where the scripts are
found, that a count is a positive whole number, and that a URL carrying a semicolon reaches a
script as a URL rather than as a second command.

### Static analysis

```shell
composer stan
```

## The rest of Quillstack

This is one component of [Quillstack](https://github.com/quillstack), a PHP framework which is
as simple to use as it is strict about what it does.

- [quillstack/cli](https://github.com/quillstack/cli) — the console this runs on
- [quillstack/output](https://github.com/quillstack/output) — what prints the results
- [quillstack/unit-tests](https://github.com/quillstack/unit-tests) — what tests it
- [quillstack/standards](https://github.com/quillstack/standards) — which requires a benchmark section in every README here

## License

MIT. See [LICENSE](LICENSE).
