<?php

declare(strict_types=1);

namespace Quillstack\Benchmark\Commands;

use Quillstack\Benchmark\Benchmark;
use Quillstack\Cli\Input;
use Quillstack\Output\OutputInterface;

final class ConsoleBenchmarkCommand extends AbstractBenchmarkCommand
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'benchmark:console';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Runs a command many times and says how long it took';
    }

    /**
     * {@inheritDoc}
     */
    public function run(Input $input, OutputInterface $output): int
    {
        $output->write(Benchmark::run('command_line.sh', [
            $this->argument($input, 0, 'command-to-run'),
            Benchmark::count($this->argument($input, 1, 'calls'), 'calls'),
            Benchmark::count($this->argument($input, 2, 'concurrency'), 'concurrency'),
        ]));

        return 0;
    }
}
