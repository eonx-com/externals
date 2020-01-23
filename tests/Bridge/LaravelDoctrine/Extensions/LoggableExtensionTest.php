<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions;

use Closure;
use EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions\LoggableExtension;
use EoneoPay\Externals\Environment\Env;
use EoneoPay\Externals\ORM\Interfaces\UserInterface;
use EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber;
use ReflectionClass;
use Tests\EoneoPay\Externals\Stubs\Auth\AuthStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\UserStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions\LoggableExtension
 */
class LoggableExtensionTest extends TestCase
{
    /**
     * LoggableExtension should add subscriber to Doctrine event manager when calling addSubscribers.
     *
     * @return void
     */
    public function testEventSubscriberIsSetInEventManager(): void
    {
        $eventManager = new EventManagerStub();
        $loggable = new LoggableExtension(new AuthStub(), new Env());

        self::assertCount(0, $eventManager->getSubscribers());

        $loggable->addSubscribers($eventManager, new EntityManagerStub());

        self::assertCount(1, $eventManager->getSubscribers());
        self::assertInstanceOf(LoggableEventSubscriber::class, $eventManager->getSubscribers()[0]);
    }

    /**
     * LoggableExtension should return an empty array when getting filters.
     *
     * @return void
     */
    public function testGetFiltersReturnsEmptyArray(): void
    {
        self::assertEmpty((new LoggableExtension(new AuthStub(), new Env()))->getFilters());
    }

    /**
     * Username resolver should return console when application used in cli.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testUsernameResolverInConsole(): void
    {
        $usernameResolver = $this->getLoggableUsernameResolver(null, null, true);

        self::assertSame('console', $usernameResolver());
    }

    /**
     * Username resolver should return testing when application used in testing.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testUsernameResolverInTesting(): void
    {
        $usernameResolver = $this->getLoggableUsernameResolver();

        self::assertSame('testing', $usernameResolver());
    }

    /**
     * Username resolver should return null when no user.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testUsernameResolverWithNoUser(): void
    {
        $usernameResolver = $this->getLoggableUsernameResolver(null, 'local');

        self::assertNull($usernameResolver());
    }

    /**
     * Username resolver should return API key id when user present.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testUsernameResolverWithUser(): void
    {
        $entity = new UserStub();

        $usernameResolver = $this->getLoggableUsernameResolver($entity, 'local');

        self::assertSame($entity->getUniqueId(), $usernameResolver());
    }

    /**
     * Get LoggableExtension usernameResolver closure for given API key.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\UserInterface|null $entity Entity to return when calling auth->user()
     * @param string|null $environment Environment to use
     * @param bool|null $console Whether this is in console or not
     *
     * @return \Closure
     *
     * @throws \ReflectionException
     */
    protected function getLoggableUsernameResolver(
        ?UserInterface $entity = null,
        ?string $environment = null,
        ?bool $console = null
    ): Closure {
        // Create env instance
        $env = new Env();
        $env->set('APP_CONSOLE', $console ?? false);
        $env->set('APP_ENV', $environment ?? 'testing');

        // Make getUsernameResolver available to ensure it's set correctly
        $class = new ReflectionClass(LoggableExtension::class);
        $method = $class->getMethod('getUsernameResolver');
        $method->setAccessible(true);

        return $method->invoke(new LoggableExtension(new AuthStub($entity), $env));
    }
}
