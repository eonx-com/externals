<?php /** @noinspection PhpPropertyNamingConventionInspection */
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 *
 * Because we're testing Doctrine proxy related behaviors
 */
class EntityProxyStub extends EntityStub
{
    /**
     * Potential property set by a proxy.
     *
     * @var mixed
     */
    public $__initializer__; // phpcs:ignore
}
