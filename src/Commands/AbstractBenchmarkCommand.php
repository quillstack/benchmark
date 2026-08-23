<?php

declare(strict_types=1);

namespace Quillstack\Benchmark\Commands;

use Quillstack\Benchmark\Exceptions\BenchmarkException;
use Quillstack\Cli\CommandInterface;
use Quillstack\Cli\Input;

abstract class AbstractBenchmarkCommand implements CommandInterface
{
    /**
     * An argument, or a refusal saying which one is missing.
     */
    protected function argument(Input $input, int $index, string $name): string
    {
        $value = $input->getArgument($index);

        if ($value === null || $value === '') {
            throw new BenchmarkException("Missing argument: {$name}");
        }

        return $value;
    }
}
