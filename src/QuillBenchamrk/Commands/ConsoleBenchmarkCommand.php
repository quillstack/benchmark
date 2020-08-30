<?php

declare(strict_types=1);

namespace QuillBenchamrk\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleBenchmarkCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'benchmark:console';

    /**
     * Root directory.
     *
     * @var string
     */
    private const SRC = __DIR__ . '/../../../';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->addArgument('command-to-run', InputArgument::REQUIRED);
        $this->addArgument('calls', InputArgument::REQUIRED);
        $this->addArgument('concurrency', InputArgument::REQUIRED);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Arguments.
        $src = self::SRC;
        $command = $input->getArgument('command-to-run');
        $calls = $input->getArgument('calls');
        $concurrency = $input->getArgument('concurrency');

        // Bash script.
        $result = shell_exec("{$src}/command_line.sh '{$command}' {$calls} {$concurrency}");

        // Output.
        $output->write($result);

        return 0;
    }
}
