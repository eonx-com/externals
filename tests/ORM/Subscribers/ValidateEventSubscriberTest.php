<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Subscribers;

use Doctrine\ORM\Events;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Tests\EoneoPay\External\ORM\Stubs\EntityWithRulesStub;
use Tests\EoneoPay\External\ORM\Stubs\EntityStub;
use Tests\EoneoPay\External\SubscribersTestCase;

class ValidateEventSubscriberTest extends SubscribersTestCase
{
    /**
     * Subscriber should return prePersist and preUpdate events.
     */
    public function testGetSubscribedEvents(): void
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->mockValidator();

        self::assertEquals(
            [Events::prePersist, Events::preUpdate],
            (new ValidateEventSubscriber($validator))->getSubscribedEvents()
        );
    }

    /**
     * Subscriber should not call validate if event object getRules does not return an array.
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException
     */
    public function testShouldNotValidateIfGetRulesNotArray(): void
    {
        $this->processNotValidateTest(new EntityWithRulesStub());
    }

    /**
     * Subscriber should not call validate if event object does not have getRules method.
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException
     */
    public function testShouldNotValidateIfNoGetRulesMethod(): void
    {
        $this->processNotValidateTest(new EntityStub());
    }

    /**
     * Subscriber should not call validate if event object is not EntityInterface.
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationException
     */
    public function testShouldNotValidateIfNotEntityInterface(): void
    {
        $this->processNotValidateTest(new \stdClass());
    }

    /**
     * Subscriber should validate if event object is EntityInterface and getRules returns an array.
     *
     * @return void
     */
    public function testShouldValidateIfInterfaceAndGetRulesIsArray(): void
    {
        $validator = $this->mockValidator();
        $validator->shouldReceive('setRules')->once()->with([])->andReturnSelf();
        $validator->shouldReceive('setData')->once()->with([])->andReturnSelf();
        $validator->shouldReceive('validate')->once()->withNoArgs()->andReturn(null);

        $object = (new EntityWithRulesStub())->setRules();

        /** @var \Doctrine\ORM\Event\LifecycleEventArgs $lifeCycleEvent */
        $lifeCycleEvent = $this->mockLifeCycleEvent($object);

        /** @var \Illuminate\Validation\Validator $validator */
        (new ValidateEventSubscriber($validator))->preUpdate($lifeCycleEvent);

        // This will only run if validation passes as an exception will be thrown
        self::assertTrue(true);
    }
}
