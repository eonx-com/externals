<?php
declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @coversNothing
 */
/** @noinspection PhpDeprecationInspection Will be removed with doctrine annotations v2.0 */
AnnotationRegistry::registerUniqueLoader('class_exists');

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
