<?php

declare(strict_types=1);

namespace Quillstack\Benchmark;

use Quillstack\Benchmark\Exceptions\BenchmarkException;

/**
 * Where the shell scripts are, and how they are run.
 *
 * They used to be found by walking up a fixed number of directories from the class file,
 * which only holds when the package sits in a plain `vendor/`. A symlinked checkout, a
 * different install path, or running from the repository itself all broke it.
 */
final class Benchmark
{
    public static function scriptsDirectory(): string
    {
        $directory = dirname(__DIR__);

        if (!is_file($directory . '/http_get.sh')) {
            throw new BenchmarkException("The benchmark scripts are not in: {$directory}");
        }

        return $directory;
    }

    /**
     * Runs one of them, with everything it is given escaped.
     *
     * @param string[] $arguments
     */
    public static function run(string $script, array $arguments): string
    {
        $path = self::scriptsDirectory() . '/' . $script;
        $command = escapeshellarg($path);

        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $result = shell_exec($command . ' 2>&1');

        return is_string($result) ? $result : '';
    }

    /**
     * A count has to be a positive whole number. Anything else would reach a shell as part of
     * a command, and would measure nothing even if it did not.
     */
    public static function count(string $value, string $name): string
    {
        if (!ctype_digit($value) || (int) $value < 1) {
            throw new BenchmarkException("{$name} must be a positive whole number, got: {$value}");
        }

        return $value;
    }
}
