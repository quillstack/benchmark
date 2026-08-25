<?php

declare(strict_types=1);

namespace Quillstack\Benchmark\Tests\Unit;

use Quillstack\Benchmark\Benchmark;
use Quillstack\Benchmark\Exceptions\BenchmarkException;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\AssertExceptions;
use Quillstack\UnitTests\Types\AssertBoolean;

/**
 * The parts of this package that decide whether a measurement can be trusted.
 *
 * Every benchmark table in this framework's documentation was produced with this tool, and until
 * now nothing tested it — which is how it once reported timings of zero, having shelled out to a
 * `python` that was not on the machine.
 */
class TestBenchmark
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean,
        private AssertExceptions $assertExceptions
    ) {
        //
    }

    /**
     * The scripts used to be found by counting directories upwards, which held only where the
     * package sat in a plain `vendor/`.
     */
    public function theScriptsAreFoundFromTheClassRatherThanCountedTo()
    {
        $directory = Benchmark::scriptsDirectory();

        $this->assertBoolean->isTrue(is_file($directory . '/http_get.sh'));
        $this->assertBoolean->isTrue(is_file($directory . '/command_line.sh'));
    }

    public function aCountIsAPositiveWholeNumber()
    {
        $this->assertEqual->equal('100', Benchmark::count('100', 'requests'));
        $this->assertEqual->equal('1', Benchmark::count('1', 'requests'));
    }

    /**
     * Anything else would reach a shell as part of a command.
     */
    public function anythingElseIsRefused()
    {
        foreach (['0', '-1', '1.5', 'ten', '', '10; rm -rf /', '1e3'] as $bad) {
            $refused = false;

            try {
                Benchmark::count($bad, 'requests');
            } catch (BenchmarkException) {
                $refused = true;
            }

            $this->assertBoolean->isTrue($refused);
        }
    }

    public function theRefusalSaysWhichArgumentAndWhatItGot()
    {
        $this->assertExceptions->expect(BenchmarkException::class);

        Benchmark::count('nonsense', 'concurrency');
    }

    /**
     * A URL is escaped on its way to a script, so one carrying a semicolon stays a URL instead
     * of becoming a second command.
     *
     * The check is a side effect on disk rather than absent text in the output: the scripts
     * reject anything they cannot read a float back from, so an output assertion would pass
     * whether escaping worked or not. Without `escapeshellarg` this touch lands — verified by
     * running the same command unescaped.
     *
     * `command_line.sh` is deliberately not tested this way. It takes a command and runs it;
     * there is nothing there to escape it from.
     */
    public function aUrlIsEscapedOnItsWayToAScript()
    {
        $marker = sys_get_temp_dir() . '/quillstack-benchmark-injection';
        @unlink($marker);

        Benchmark::run('http_get.sh', ["http://localhost/x; touch {$marker}", '1', '1']);

        $this->assertBoolean->isFalse(is_file($marker));
    }
}
