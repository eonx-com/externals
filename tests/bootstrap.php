<?php
declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

// Until Doctrine Annotations v2.0, we need to register an autoloader, which is just 'class_exists'.
AnnotationRegistry::registerUniqueLoader('class_exists');

/**
 * @coversNothing
 */
if (\function_exists('xdebug_set_filter') === false) {
    return;
}

/** @noinspection PhpUndefinedConstantInspection Constants are only defined if xdebug if loaded */
/** @noinspection PhpUndefinedFunctionInspection Function definition is checked above */
\xdebug_set_filter(
    \XDEBUG_FILTER_CODE_COVERAGE,
    \XDEBUG_PATH_WHITELIST,
    [\sprintf('%s/src', \dirname(__DIR__))]
);
