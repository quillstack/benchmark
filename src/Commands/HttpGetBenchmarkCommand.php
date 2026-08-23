<?php

declare(strict_types=1);

namespace Quillstack\Benchmark\Commands;

use Quillstack\Benchmark\Benchmark;
use Quillstack\Cli\Input;
use Quillstack\Output\OutputInterface;

final class HttpGetBenchmarkCommand extends AbstractBenchmarkCommand
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'benchmark:http:get';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Asks a URL for a page many times and says how long it took';
    }

    /**
     * {@inheritDoc}
     */
    public function run(Input $input, OutputInterface $output): int
    {
        $output->write(Benchmark::run('http_get.sh', [
            $this->argument($input, 0, 'url'),
            Benchmark::count($this->argument($input, 1, 'requests'), 'requests'),
            Benchmark::count($this->argument($input, 2, 'concurrency'), 'concurrency'),
        ]));

        return 0;
    }
}
