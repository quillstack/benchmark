<?php

declare(strict_types=1);

namespace Quillstack\Benchmark\Commands;

use Quillstack\Benchmark\Benchmark;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'benchmark:http:get',
    description: 'Asks a URL for a page many times and says how long it took'
)]
final class HttpGetBenchmarkCommand extends AbstractBenchmarkCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'The URL to ask');
        $this->addArgument('requests', InputArgument::REQUIRED, 'How many times');
        $this->addArgument('concurrency', InputArgument::REQUIRED, 'How many at once');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write(Benchmark::run('http_get.sh', [
            $this->text($input, 'url'),
            Benchmark::count($this->text($input, 'requests'), 'requests'),
            Benchmark::count($this->text($input, 'concurrency'), 'concurrency'),
        ]));

        return Command::SUCCESS;
    }
}
