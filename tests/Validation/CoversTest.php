<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Validation;

use PHPUnit\Framework\TestCase;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;

/**
 * @coversNothing
 */
class CoversTest extends TestCase
{
    /**
     * Test all tests contains a covers* annotation
     *
     * @return void
     */
    public function testAllTestsContainCoversAnnotation(): void
    {
        // Get all test files in the tests directory
        $path = \realpath(\dirname(__DIR__));
        $filenames = $this->getTestFilenames($path);

        // Group all failures together
        $failures = [];

        foreach ($filenames as $filename) {
            // Read file
            $contents = \file($filename);

            // If file is unreadable, skip
            if ($contents === false) {
                continue;
            }

            foreach ($contents as $line) {
                if (\strncmp($line, ' * @covers', 10) === 0) {
                    continue 2;
                }
            }

            $failures[] = \sprintf(
                'Test file (%s) does not contain @covers or @coversNothing',
                \str_replace($path, 'tests', $filename)
            );
        }

        // If there are failures, fail
        if (\count($failures) > 0) {
            self::fail(\implode(\PHP_EOL, $failures));
        }

        // All good, increment count
        $this->addToAssertionCount(1);
    }

    /**
     * Get test files from the tests directory
     *
     * @param string $path The path to search within
     *
     * @return string[]
     */
    private function getTestFilenames(string $path): array
    {
        // Filter stubs
        $filter = static function (SplFileInfo $file) {
            return \strpos($file->getPathname(), '/tests/Stubs') === false;
        };

        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator($directory, $filter));
        $matches = new RegexIterator($iterator, '/.*\.php$/', RegexIterator::GET_MATCH);

        $filenames = [];

        foreach ($matches as $files) {
            $filenames[] = $files;
        }

        return \count($filenames) === 0 ? [] : \array_merge(... $filenames);
    }
}
