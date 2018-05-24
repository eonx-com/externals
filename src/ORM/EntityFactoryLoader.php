<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityFactoryLoaderInterface;

class EntityFactoryLoader implements EntityFactoryLoaderInterface
{
    /**
     * List of paths where to find entity factories.
     *
     * @var string[]
     */
    private $paths;

    /**
     * EntityFactoryLoader constructor.
     *
     * @param string[] $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Return the list of available factories class names.
     *
     * @return string[]
     *
     * @throws \ReflectionException
     */
    public function loadFactoriesClassNames(): array
    {
        $classes = [];
        $includedFiles = [];

        foreach ($this->paths as $path) {
            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0] ?? '';

                /** @noinspection PhpIncludeInspection Must require files dynamically */
                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }

        foreach (\get_declared_classes() as $className) {
            if (\in_array((new \ReflectionClass($className))->getFileName(), $includedFiles, true)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }
}
