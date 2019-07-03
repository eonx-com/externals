<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Constraints;

use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * This annotation extends base comparison validators with the
 * ability to compare string dates instead of just DateTime objects.
 *
 * @noinspection EmptyClassInspection
 *
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class DateGreaterThan extends GreaterThan
{
}
