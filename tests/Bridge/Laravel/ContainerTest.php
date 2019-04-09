<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Container;
use Illuminate\Container\Container as IlluminateContainer;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Container
 */
class ContainerTest extends TestCase
{
    /**
     * Container should use illuminate container to retrieve services.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If container item doesn't exist
     */
    public function testContainerForIlluminate(): void
    {
        $closure = static function (): string {
            return 'value';
        };

        $illuminate = new IlluminateContainer();
        $illuminate->instance('test', $closure);

        $container = new Container($illuminate);

        self::assertTrue($container->has('test'));
        self::assertFalse($container->has('invalid'));

        self::assertSame($closure, $container->get('test'));
    }
}
