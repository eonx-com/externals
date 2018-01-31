<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Mockery;
use Mockery\MockInterface;

abstract class SubscribersTestCase extends TestCase
{
    /**
     * Mock Doctrine life cycle event with getObject expectation returning given object.
     *
     * @param $object
     *
     * @return \Mockery\MockInterface
     */
    protected function mockLifeCycleEvent($object): MockInterface
    {
        $event = Mockery::mock(LifecycleEventArgs::class);
        $event->shouldReceive('getObject')->once()->withNoArgs()->andReturn($object);

        return $event;
    }

    /**
     * Mock Illuminate validation factory.
     *
     * @return \Mockery\MockInterface
     */
    protected function mockValidationFactory(): MockInterface
    {
        return Mockery::mock(ValidationFactory::class);
    }

    /**
     * Mock Illuminate validator.
     *
     * @return \Mockery\MockInterface
     */
    protected function mockValidator(): MockInterface
    {
        return Mockery::mock(Validator::class);
    }

    /**
     * Process test when subscriber should not validate.
     *
     * @param $object
     */
    protected function processNotValidateTest($object): void
    {
        $factory = $this->mockValidationFactory();
        $factory->shouldNotReceive('make');

        self::assertNull((new ValidateEventSubscriber($factory))->prePersist($this->mockLifeCycleEvent($object)));
    }
}
