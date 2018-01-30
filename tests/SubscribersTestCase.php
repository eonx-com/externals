<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Illuminate\Validation\Validator;
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
        $validator = $this->mockValidator();
        $validator->shouldNotReceive(['setRules', 'setData', 'validate']);

        self::assertNull((new ValidateEventSubscriber($validator))->prePersist($this->mockLifeCycleEvent($object)));
    }
}
