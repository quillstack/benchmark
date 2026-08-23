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
    name: 'benchmark:console',
    description: 'Runs a command many times and says how long it took'
)]
final class ConsoleBenchmarkCommand extends AbstractBenchmarkCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('command-to-run', InputArgument::REQUIRED, 'What to run');
        $this->addArgument('calls', InputArgument::REQUIRED, 'How many times');
        $this->addArgument('concurrency', InputArgument::REQUIRED, 'How many at once');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write(Benchmark::run('command_line.sh', [
            $this->text($input, 'command-to-run'),
            Benchmark::count($this->text($input, 'calls'), 'calls'),
            Benchmark::count($this->text($input, 'concurrency'), 'concurrency'),
        ]));

        return Command::SUCCESS;
    }
}
