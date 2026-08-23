<?php

declare(strict_types=1);

namespace Quillstack\Benchmark\Commands;

use Quillstack\Benchmark\Exceptions\BenchmarkException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractBenchmarkCommand extends Command
{
    /**
     * An argument, as the string it has to be.
     *
     * The console hands them back as `mixed`, and the value goes on to be part of a command
     * line — so what it is has to be checked rather than assumed.
     */
    protected function text(InputInterface $input, string $name): string
    {
        $value = $input->getArgument($name);

        if (!is_string($value)) {
            throw new BenchmarkException("{$name} has to be given as text");
        }

        return $value;
    }
}
