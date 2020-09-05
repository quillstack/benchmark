<?php

declare(strict_types=1);

namespace QuillStack\Benchmark\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class HttpGetBenchmarkCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'benchmark:http:get';

    /**
     * Root directory.
     *
     * @var string
     */
    private const SRC = __DIR__ . '/../../../../../benchmark/';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->addArgument('url', InputArgument::REQUIRED);
        $this->addArgument('requests', InputArgument::REQUIRED);
        $this->addArgument('concurrency', InputArgument::REQUIRED);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Arguments.
        $src = self::SRC;
        $url = $input->getArgument('url');
        $requests = $input->getArgument('requests');
        $concurrency = $input->getArgument('concurrency');

        // Bash script.
        $result = shell_exec("{$src}/http_get.sh '{$url}' {$requests} {$concurrency}");

        // Output.
        $output->write($result);

        return 0;
    }
}
